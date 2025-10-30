<?php

namespace app\Models;

use config\DBConnection;
use PDO;

class EmailModel
{
    private $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db->getConnection();
    }
    
    // Add your custom methods below to interact with the database.
     public function save($email, $code)
    {
        $stmt = $this->db->prepare("INSERT INTO email_verifications (email, code, created_at) VALUES (:email,:code , NOW()) 
        ON DUPLICATE KEY UPDATE code = VALUES(code), created_at = NOW()");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM email_verifications WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkEmailExists($email)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM members WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function delete($email)
    {
        $stmt = $this->db->prepare("DELETE FROM email_verifications WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    }
}