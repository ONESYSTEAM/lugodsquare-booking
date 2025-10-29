<?php

namespace app\Controllers;

use config\DBConnection;
use app\Models\MembershipModel;

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

                echo $GLOBALS['templates']->render('Membership-pin');
                exit;
            }
        }
    }

    public function setPin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pin = $_POST['pin'];
            $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

            $set_pin = $this->MembershipModel->setPin($hashedPin, $_SESSION['membership_id']);
            if ($set_pin) {
                echo $GLOBALS['templates']->render('Membership-confirmation');
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
                    'contactNum' => $member['contact_number']
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
