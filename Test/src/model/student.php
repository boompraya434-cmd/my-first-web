<?php
    // ติดต่อฐานข้อมูลเพื่ออ่านข้อมูลนักศึกษา
    class student {
        // เก็บ PDO connection ที่ controller ส่งเข้ามา
        private PDO $conn;

        // constructor รับ connection สำหรับ query
        public function __construct(PDO $db){
            $this->conn = $db;
        }

        // อ่านแถวทั้งหมดจากตาราง students แล้วคืนเป็น associative arrays
        public function getAll(): array{
            $sql = 'SELECT * FROM students';
            // prepare เตรียมคำสั่ง SQL ก่อน execute
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            // คืนผลลัพธ์ทั้งหมด โดยใช้ชื่อคอลัมน์เป็น key
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>