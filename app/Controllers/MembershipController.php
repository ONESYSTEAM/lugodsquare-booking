<?php

namespace app\Controllers;

use config\DBConnection;
use app\Models\MembershipModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MembershipController
{
    private $MembershipModel;

    public function __construct()
    {
        $db = new DBConnection();
        $this->MembershipModel = new MembershipModel($db);
    }

    // Add your custom controllers below to handle business logic.
    public function add()
    {
        if (isset($_POST['sumbitRegistration']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $contactNum = $_POST['contactNum'];
            $email = $_POST['email'];
            $birthDate = $_POST['birthDate'];
            $address = $_POST['address'];

            $MembershipId = $this->MembershipModel->getMemberId();
            if ($MembershipId && isset($MembershipId['membership_id'])) {
                $lastMembershipId = $MembershipId['membership_id'];
                $id = (int)substr($lastMembershipId, -4);
                $newId = str_pad($id + 1, 4, '0', STR_PAD_LEFT);
                $year = date("Y");
                $generatedId = "CBMS-$year-$newId";
            } else {
                $year = date("Y");
                $generatedId = "CBMS-$year-0001";
            }

            $addMember = $this->MembershipModel->addMembership($firstName, $lastName, $birthDate, $address, $contactNum, $email, $generatedId);

            if ($addMember) {
                $member = $this->MembershipModel->getMemberById($generatedId);
                $_SESSION['first_name'] = $member['first_name'];
                $_SESSION['last_name'] = $member['last_name'];
                $_SESSION['membership_id'] = $member['membership_id'];

                header("Location:/membership-pin");
                exit;
            }
        }
    }

    public function setPin()
    {
        if (isset($_POST['submitPinBtn'])) {
            $pin = $_POST['pin'];
            $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
            $membershipId = $_POST['membershipId'];

            $subject = "Membership Confirmation";
            $appName = $_ENV['APP_NAME'] ?? '';

            $body = "
            <div style='font-family: Arial, sans-serif; background-color: #f6f8fa; padding: 20px;'>
                <div style='max-width:600px;margin:auto;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
                    <div style='background-color: #dc3545;color:white;text-align:center;padding:20px;'>
                        <h2 style='margin:0;'>Court Booking Confirmation</h2>
                    </div>
                    <div style='padding:25px;'>
                        <p>We're happy to inform you that your membership has been successfully confirmed.</p>

                        <div style='background-color:#f9fafc;padding:15px;border-radius:6px;margin:15px 0;'>
                            <p><strong>Membership ID:</strong> $membershipId </p>
                        </div>

                        <p>To get your physical ID, drop by our office and bring a small fee for processing.</p>
                        <p>Thank you for choosing <strong>$appName</strong> — we look forward to seeing you!</p>

                        <hr style='border:none;border-top:1px solid #ddd;margin:20px 0;'>

                        <p style='font-size:13px;color:#777;text-align:center;'>This is an automated message, please do not reply.<br>
                        &copy; " . date('Y') . " Lugod Square. All rights reserved.</p>
                    </div>
                </div>
            </div>
            ";

            $this->MembershipModel->setPin($hashedPin, $_SESSION['membership_id']);

            $member = $this->MembershipModel->getMemberByPin($membershipId, $hashedPin);
            $email = $member['email'];

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
                header("Location:/confirmation");
            } catch (Exception $e) {
                error_log("Email error: " . $mail->ErrorInfo);
            }
        }
    }

    public function checkMembership()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $membershipId = trim($_POST['membership_id']);

            $member = $this->MembershipModel->getMemberById($membershipId);
            if ($member) {
                echo json_encode([
                    'status' => 'success'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Membership ID not found']);
            }
        }
    }

    public function checkMembershipPin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $membershipId = trim($_POST['membership_id']);
            $pin = trim($_POST['pin']);

            $member = $this->MembershipModel->getMemberById($membershipId);
            if (password_verify($pin, $member['pin'])) {
                echo json_encode([
                    'status' => 'success',
                    'firstName' => $member['first_name'],
                    'lastName' => $member['last_name'],
                    'email' => $member['email'],
                    'contactNum' => $member['contact_number'],
                    'wallet' => $member['wallet']
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Incorrect Pin']);
            }
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        header("Location:/");
        exit;
    }
}
