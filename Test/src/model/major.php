<?php
    // ติดต่อฐานข้อมูลเพื่ออ่านข้อมูลนักศึกษา
    class major {
        // เก็บ PDO connection ที่ controller ส่งเข้ามา
        private PDO $conn;

        // constructor รับ connection สำหรับ query
        public function __construct(PDO $db){
            $this->conn = $db;
        }

        // อ่านนักศึกษาของสาขาที่ระบุ แล้วคืนเป็น associative arrays
        public function getAll(string $major): array{
            $sql = 'SELECT * FROM students WHERE major = :major';
            // prepare เตรียมคำสั่ง SQL ก่อน execute
            $stmt = $this->conn->prepare($sql);
            // ส่งชื่อสาขาให้ placeholder :major เพื่อไม่ให้อ้างถึงตัวแปรที่ไม่ได้กำหนด
            $stmt->execute(['major' => $major]);

            // คืนผลลัพธ์ทั้งหมด โดยใช้ชื่อคอลัมน์เป็น key
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>