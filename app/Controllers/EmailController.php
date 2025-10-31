<?php

namespace app\Controllers;

use config\DBConnection;
use app\Models\EmailModel;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController
{
    private $EmailModel;

    public function __construct()
    {
        $db = new DBConnection();
        $this->EmailModel = new EmailModel($db);
    }
    
    public function verifyEmail()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);

            $checkEmailExists = $this->EmailModel->checkEmailExists($email);

            if ($checkEmailExists) {
                echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['valid' => false, 'message' => 'Invalid email format.']);
                return;
            }

            // Check domain MX record
            $domain = substr(strrchr($email, "@"), 1);
            if (!checkdnsrr($domain, "MX")) {
                echo json_encode(['valid' => false, 'message' => 'Invalid email domain.']);
                return;
            }

            // Generate numeric code and save to DB
            $code = random_int(100000, 999999);
            $this->EmailModel->save($email, $code);

            //  Send the code
            if ($this->sendCode($email, $code)) {
                echo json_encode(['valid' => true, 'message' => 'Verification code sent to your email.']);
            } else {
                echo json_encode(['valid' => false, 'message' => 'Failed to send verification code.']);
            }
        }
    }

    public function confirmCode()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $code = trim($_POST['code']);

            $verification = $this->EmailModel->findByEmail($email);

            if (!$verification) {
                echo json_encode(['success' => false, 'message' => 'No verification record found.']);
                return;
            }
            // Expired (older than 10 minutes)
            $createdAt = strtotime($verification['created_at']);
            if (time() - $createdAt > 600) {
                $this->EmailModel->delete($email);
                echo json_encode(['success' => false, 'message' => 'Code expired. Please request a new one.']);
                return;
            }

            // Invalid code
            if ($verification['code'] !== $code) {
                echo json_encode(['success' => false, 'message' => 'Invalid code.']);
                return;
            }

            // ✅ Code is correct → remove record and allow form submission 
            $this->EmailModel->delete($email);
            echo json_encode(['success' => true, 'message' => 'Email verified successfully!']);
        }
    }

    private function sendCode($email, $code)
    {
        $subject = "Your Verification Code";
        $body = "
            <h2>Email Verification</h2>
            <p>Use this code to verify your email:</p>
            <h1 style='letter-spacing:4px;'>$code</h1>
            <p>This code expires in 10 minutes.</p>
        ";

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->Port = $_ENV['MAIL_PORT'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = 'tls';

            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
