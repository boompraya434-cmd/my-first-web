<?php
    // โหลด controller สำหรับแสดงข้อมูลนักศึกษา
    require_once __DIR__ . '/controllers/studentController.php';

    // สร้าง controller ซึ่งจะเชื่อมต่อฐานข้อมูลและเรียก model นักศึกษา
    $controller = new studentController();

    // เรียก index เพื่อดึงข้อมูลนักศึกษาและแสดง view
    $controller->index();
?>