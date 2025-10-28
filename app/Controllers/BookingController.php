<?php

namespace app\Controllers;

use config\DBConnection;
use app\Models\BookingModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BookingController
{
    private $BookingModel;

    public function __construct()
    {
        $db = new DBConnection();
        $this->BookingModel = new BookingModel($db);
    }

    // Add your custom controllers below to handle business logic.

    public function index()
    {
        $courts = $this->BookingModel->getCourts();
        echo $GLOBALS['templates']->render('Booking', ['courts' => $courts]);
    }

    public function getCourtDetails()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courtType = $_POST['court_type'] ?? '';

            $court = $this->BookingModel->getCourtByType($courtType);

            if ($court) {
                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'capacity' => $court['capacity'],
                        'amount' => $court['amount']
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }
    public function calculateTotal()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start = $_POST['start_time'] ?? '';
            $end = $_POST['end_time'] ?? '';
            $rate = floatval($_POST['rate'] ?? 0);
            $isMember = filter_var($_POST['isMember'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $discountRate = floatval($_POST['discountRate'] ?? 0);

            if (!$start || !$end || $rate <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Missing data']);
                return;
            }

            $startTime = strtotime($start);
            $endTime = strtotime($end);

            if ($endTime <= $startTime) {
                echo json_encode(['status' => 'error', 'message' => 'End time must be after start time']);
                return;
            }

            $durationHours = ($endTime - $startTime) / 3600; 
            $subtotal = round($durationHours * $rate, 2);
            $discount = 0;

            if ($isMember) {
                $discount = $subtotal * $discountRate;
            }

            $total = $subtotal - $discount;

            echo json_encode([
                'status' => 'success',
                'subtotal' => number_format($subtotal, 2),
                'discount' => number_format($discount, 2),
                'total' => number_format($total, 2)
            ]);
        }
    }

    public function booking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membershipId = $_POST['membershipId'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $contactNum = $_POST['contactNum'];
            $email = $_POST['email'];
            $courtType = $_POST['courtType'];
            $date = $_POST['date'];
            $startTime = $_POST['startTime'];
            $endTime = $_POST['endTime'];
            $totalAmount = $_POST['total'];

            $result = $this->BookingModel->insertBooking(
                $membershipId,
                $firstName,
                $lastName,
                $contactNum,
                $email,
                $courtType,
                $date,
                $startTime,
                $endTime,
                $totalAmount
            );

            header('Content-Type: application/json');
            $court = $this->BookingModel->getCourtByType($courtType);

            if ($result) {
                ob_start();
                try {
                    $name = $firstName . ' ' . $lastName;
                    $sent = $this->sendBookingConfirmationEmail(
                        $email,
                        $name,
                        $court['court_type'],
                        $date,
                        $startTime,
                        $endTime,
                        $totalAmount
                    );
                    if (!$sent) {
                        error_log("Failed to send confirmation email to $email");
                    }
                } catch (Exception $e) {
                    error_log("Email exception: " . $e->getMessage());
                }
                ob_end_clean();

                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database insert failed.']);
            }
            exit;
        }
    }


    public function getBookedSlots()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $court = $_POST['court'] ?? null;
            $date = $_POST['date'] ?? null;

            $slots = $this->BookingModel->bookedSlots($court, $date);
            if ($slots) {
                echo json_encode(['bookedSlots' => $slots]);
                exit;
            }
        }
    }

    private function sendBookingConfirmationEmail($email, $name, $courtType, $date, $startTime, $endTime, $totalAmount)
    {
        $subject = "Court Booking Confirmation - ".$courtType." Court";

        $formattedDate = date('F j, Y', strtotime($date));
        $formattedStart = date('g:i A', strtotime($startTime));
        $formattedEnd = date('g:i A', strtotime($endTime));
        $formattedAmount = $totalAmount;

        $body = "
        <div style='font-family: Arial, sans-serif; background-color: #f6f8fa; padding: 20px;'>
            <div style='max-width:600px;margin:auto;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
                <div style='background-color:#2c7a7b;color:white;text-align:center;padding:20px;'>
                    <h2 style='margin:0;'>Court Booking Confirmation</h2>
                </div>
                <div style='padding:25px;'>
                    <p>Hi <strong>$name</strong>,</p>
                    <p>We’re happy to inform you that your court booking has been successfully confirmed.</p>

                    <div style='background-color:#f9fafc;padding:15px;border-radius:6px;margin:15px 0;'>
                        <p><strong>Court Type:</strong> $courtType</p>
                        <p><strong>Date:</strong> $formattedDate</p>
                        <p><strong>Time:</strong> $formattedStart - $formattedEnd</p>
                        <p><strong>Total Amount:</strong> ₱$formattedAmount</p>
                    </div>

                    <p>Please arrive at least <strong>10 minutes before</strong> your scheduled time.</p>
                    <p>Thank you for choosing <strong>Lugod Square</strong> — we look forward to seeing you!</p>

                    <hr style='border:none;border-top:1px solid #ddd;margin:20px 0;'>

                    <p style='font-size:13px;color:#777;text-align:center;'>This is an automated message, please do not reply.<br>
                    &copy; " . date('Y') . " Lugod Square. All rights reserved.</p>
                </div>
            </div>
        </div>
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
