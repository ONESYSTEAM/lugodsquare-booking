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

    public function getMemberAccountDetails($cardNumber)
    {
        // 1. Get Basic Member Profile
        $stmt = $this->db->prepare("SELECT id, first_name, last_name, email, contact_number, wallet 
                                FROM members 
                                WHERE card_number = :card_number LIMIT 1");
        $stmt->bindParam(':card_number', $cardNumber, PDO::PARAM_STR);
        $stmt->execute();
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member) return null;

        // 2. Get Transaction History with Item Summary (Handles multiple items per sale)
        $stmt = $this->db->prepare("
        SELECT 
            s.id AS sale_id, 
            s.created_at, 
            s.final_total, 
            s.payment_method,
            GROUP_CONCAT(CONCAT(si.qty, 'x ', si.item_name) SEPARATOR ', ') AS item_summary
        FROM sales s
        LEFT JOIN sales_items si ON s.id = si.sale_id
        WHERE s.membership_card = :card_number
        GROUP BY s.id
        ORDER BY s.created_at DESC
    ");
        $stmt->bindParam(':card_number', $cardNumber, PDO::PARAM_STR);
        $stmt->execute();

        $member['transactions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $member;
    }
}
