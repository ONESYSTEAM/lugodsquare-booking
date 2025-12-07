<?php

namespace app\Models;

use config\DBConnection;
use PDO;

class BookingModel
{
    private $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db->getConnection();
    }

    public function getCourts()
    {
        $stmt = $this->db->prepare("SELECT * FROM courts");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourtByType($courtType)
    {
        $stmt = $this->db->prepare("SELECT * FROM courts WHERE id = :court");
        $stmt->bindParam(':court', $courtType, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertBooking($membershipId, $firstName, $lastName, $contactNum, $email, $courtType, $date, $startTime, $endTime, $totalAmount, $receipt)
    {
        $stmt = $this->db->prepare("INSERT INTO booking (membership_id, first_name, last_name, contact_number, email, court_type, date, start_time, end_time, total_amount, gcash_receipt, booked_at, status)
        VALUES (:id, :fname, :lname, :num, :email, :court, :date, :stime, :etime, :total, :receipt, NOW(), 0)");
        $stmt->bindParam(':id', $membershipId, PDO::PARAM_STR);
        $stmt->bindParam(':fname', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lname', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':num', $contactNum, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':court', $courtType, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':stime', $startTime, PDO::PARAM_STR);
        $stmt->bindParam(':etime', $endTime, PDO::PARAM_STR);
        $stmt->bindParam(':total', $totalAmount, PDO::PARAM_STR);
        $stmt->bindParam(':receipt', $receipt, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function bookedSlots($court, $date)
    {
        $stmt = $this->db->prepare("SELECT start_time, end_time FROM booking WHERE court_type = :court AND date = :date");
        $stmt->bindParam(':court', $court, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateWalletBalance($membershipId, $newBalance)
    {
        $stmt = $this->db->prepare("UPDATE members SET wallet = :balance WHERE membership_id = :id");
        $stmt->bindParam(':balance', $newBalance, PDO::PARAM_STR);
        $stmt->bindParam(':id', $membershipId, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
