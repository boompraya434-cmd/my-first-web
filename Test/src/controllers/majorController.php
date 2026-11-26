<?php
    // โหลดไฟล์เชื่อมต่อฐานข้อมูลและ model เพียงครั้งเดียว
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../model/major.php';

    // รับคำขอหน้า student และประสานงานระหว่าง model กับ view
    class majorController {
        // model ที่ใช้ค้นหารายชื่อนักศึกษา
        private Student $students;

        public function __construct()
        {
            // เปิด PDO connection แล้วส่งให้ model ใช้
            $database = new database();
            $db = $database->connect();

            $this->students = new student($db);
        }

        public function index(): void {
            // ดึงรายชื่อทั้งหมด แล้วส่งตัวแปร $student ให้ view แสดงผล
            $student = $this->students->getAll();
            // โหลด template ของหน้ารายชื่อนักศึกษา
            require __DIR__ . '/../views/student/index.php';
        }
    }
?>