<?php

namespace app\Models;

use config\DBConnection;
use PDO;

class MembershipModel
{
    private $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db->getConnection();
    }

    // Add your custom methods below to interact with the database.
    public function getMemberId()
    {
        $stmt = $this->db->prepare("SELECT membership_id FROM members ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addMembership($firstName, $lastName, $birthDate, $address, $contactNum, $email, $membershipId)
    {
        $stmt = $this->db->prepare("INSERT INTO members (first_name, last_name, address, birth_date, contact_number, email, membership_id, wallet, joined_at ) 
        VALUES (:first_name, :last_name, :address, :birth_date, :contact_number, :email,:membership_id, '0.00', NOW())");
        $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':birth_date', $birthDate, PDO::PARAM_STR);
        $stmt->bindParam(':contact_number', $contactNum, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':membership_id', $membershipId, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function setPin($pin, $membershipId)
    {
        $stmt = $this->db->prepare("UPDATE members SET pin = :pin WHERE membership_id = :membership_id");
        $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
        $stmt->bindParam(':membership_id', $membershipId, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getMemberById($membershipId)
    {
        $stmt = $this->db->prepare("SELECT * FROM members WHERE membership_id = :membership_id");
        $stmt->bindParam(':membership_id', $membershipId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getMemberByPin($membershipId, $pin)
    {
        $stmt = $this->db->prepare("SELECT * FROM members WHERE membership_id = :membership_id AND pin = :pin");
        $stmt->bindParam(':membership_id', $membershipId, PDO::PARAM_STR);
        $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
