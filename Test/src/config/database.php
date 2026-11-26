<?php
// รวมค่าการเชื่อมต่อและสร้าง PDO connection ให้ controller/model ใช้
class database{
    // ค่า fallback ใช้เมื่อไม่ได้กำหนด environment variables
    private string $db = "studentdb";
    private string $username = "admin";
    private string $password = "admin123";

    public function connect(): PDO {
        // ใน Docker ใช้ DB_HOST=db; บนเครื่องใช้ 127.0.0.1
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        // พอร์ตภายใน Docker คือ 3306; บนเครื่องใช้พอร์ตที่ Compose เปิดไว้คือ 3308
        $port = getenv('DB_PORT') ?: '3308';
        // ให้ค่าจาก environment มาก่อน แล้วใช้ค่า fallback ถ้าไม่มี
        $db = getenv('DB_NAME') ?: $this->db;
        $username = getenv('DB_USER') ?: $this->username;
        $password = getenv('DB_PASSWORD') ?: $this->password;
        // DSN ระบุชนิดฐานข้อมูล, host, port, ชื่อ database และ charset
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        
        // สร้างการเชื่อมต่อ PDO และตั้งค่าการจัดการ error/ผลลัพธ์
        return new PDO(
            $dsn,
            $username,
            $password,
            [
                // ให้ PDO โยน exception เมื่อ query ผิดพลาด
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // ให้ผลลัพธ์จาก query เป็น associative array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // ใช้ prepared statements จริงจาก MySQL
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
?>