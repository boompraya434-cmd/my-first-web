<?php
// เริ่ม session เพื่อเก็บข้อมูลที่ผู้ใช้ส่งไว้ระหว่างเปิดเว็บ
session_start();

// แบบจำลองข้อมูลนักศึกษา: กำหนดชนิดข้อมูลของแต่ละค่าที่ใช้ในหน้านี้
class Student
{
    // เก็บข้อมูลแต่ละช่อง พร้อมระบุชนิดข้อมูลเพื่อช่วยตรวจค่าที่ส่งเข้ามา
    public string $stdid;
    public string $stdname;
    public int $age;
    public string $department;
    public float $gpa;
    public string $advisor;
    public string $phone;
    public string $room;
    public string $address;

    // รับข้อมูลแล้วเก็บลง property ของ object นักศึกษา
    public function __construct(string $stdid, string $stdname, int $age, string $department, float $gpa, string $advisor, string $phone, string $room, string $address)
    {
        $this->stdid = $stdid;
        $this->stdname = $stdname;
        $this->age = $age;
        $this->department = $department;
        $this->gpa = $gpa;
        $this->advisor = $advisor;
        $this->phone = $phone;
        $this->room = $room;
        $this->address = $address;
    }

    // รวมข้อมูลนักศึกษาเป็นข้อความสำหรับแสดงบนหน้าเว็บ
    public function getStudentInfo(): string
    {
        return "รหัสนักศึกษา: {$this->stdid}\nชื่อ: {$this->stdname}\nอายุ: {$this->age}\nแผนกวิชา: {$this->department}\nเกรดเฉลี่ย: {$this->gpa}\nครูที่ปรึกษา: {$this->advisor}\nเบอร์: {$this->phone}\nห้อง: {$this->room}\nที่อยู่: {$this->address}";
    }
}

// ข้อมูลตัวอย่างที่ใช้แสดงเมื่อยังไม่มีข้อมูลจากฟอร์ม
$defaultStudent = new Student("69319010003", "นายทินกร พุ่มยิ้ม", 18, "เทคโนโลยีสารสนเทศ", 3.75, "นางสาวสมคิด หวังดี", "081-234-5678", "A-305", "123/45 ถนนสยาม แขวงบางรัก กรุงเทพมหานคร");
// อ่านข้อมูลล่าสุดจาก session; ?? null คือใช้ null เมื่อยังไม่มีข้อมูล
$submittedStudent = $_SESSION['submitted_student'] ?? null;

// ประมวลผลเฉพาะคำขอ POST ซึ่งเกิดจากการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // isset ตรวจว่ามีการส่งปุ่มลบมาหรือไม่
    // ปุ่มลบจะล้างข้อมูลที่เก็บไว้ใน session
    if (isset($_POST['delete_student'])) {
        // unset ลบ key นี้ออกจาก session
        unset($_SESSION['submitted_student']);
        $submittedStudent = null;
    // ตรวจว่าคำขอมีรหัสนักศึกษาจากฟอร์มบันทึกหรือไม่
    } elseif (isset($_POST['stdid'])) {
        // trim ตัดช่องว่างหัวท้ายก่อนนำค่าจากฟอร์มไปตรวจสอบ
        $stdid = trim($_POST['stdid']);
        $stdname = trim($_POST['stdname']);
        // filter_var ตรวจว่าค่าอายุและ GPA แปลงเป็นตัวเลขชนิดที่ต้องการได้
        $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
        $department = trim($_POST['department'] ?? '');
        $gpa = filter_var($_POST['gpa'] ?? '', FILTER_VALIDATE_FLOAT);
        $advisor = trim($_POST['advisor'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $room = trim($_POST['room'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // ตรวจว่ากรอกข้อมูลครบ อายุ 1-100, GPA 0-4 และเบอร์โทรมีแต่ตัวเลขไม่เกิน 10 หลัก
        if ($stdid !== '' && $stdname !== '' && $age !== false && $age >= 1 && $age <= 100 && $department !== '' && $gpa !== false && $gpa >= 0 && $gpa <= 4 && $advisor !== '' && $phone !== '' && strlen($phone) <= 10 && preg_match('/^[0-9]+$/', $phone) && $room !== '' && $address !== '') {
            // สร้าง object จากข้อมูลที่ผ่านการตรวจ แล้วเก็บไว้ใน session (ยังไม่บันทึกลง MySQL)
            $submittedStudent = new Student($stdid, $stdname, $age, $department, $gpa, $advisor, $phone, $room, $address);
            $_SESSION['submitted_student'] = $submittedStudent;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- กำหนดขนาดหน้าให้เหมาะกับหน้าจอมือถือและคอมพิวเตอร์ -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <style>
        /* ตัวแปรสีหลักที่ใช้ซ้ำในหลายส่วนของหน้า */
        :root {
            --bg: #020817;
            --panel: rgba(10, 17, 30, 0.88);
            --panel-strong: rgba(13, 20, 34, 0.96);
            --primary: #7c3aed;
            --secondary: #22d3ee;
            --text: #e6f3ff;
            --muted: #8ea6c7;
            --line: rgba(110, 231, 255, 0.18);
        }

        /* รวม padding และเส้นขอบไว้ในขนาดที่กำหนดของ element */
        * { box-sizing: border-box; }

        /* กำหนดภาพเคลื่อนไหวให้เนื้อหาเลื่อนขึ้นพร้อม fade */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ทำแสงเงาให้ปุ่มและแผงดูเรืองแสงเป็นจังหวะ */
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 rgba(124, 58, 237, 0.35);
            }
            50% {
                box-shadow: 0 0 24px rgba(34, 211, 238, 0.26);
            }
        }

        /* จัดพื้นหลังและวางเนื้อหาหลักให้อยู่กลางหน้า */
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                linear-gradient(rgba(15, 23, 42, 0.55), rgba(2, 8, 23, 0.92)),
                radial-gradient(circle at top left, rgba(124, 58, 237, 0.4), transparent 32%),
                radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.28), transparent 26%),
                var(--bg);
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 32px 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* ลายตารางตกแต่งพื้นหลัง ไม่รับการคลิก */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(34, 211, 238, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 211, 238, 0.05) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,0.75) 1px, transparent 1.6px);
            background-size: 38px 38px, 38px 38px, 14px 14px;
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
        }

        /* วงแสงตกแต่งพื้นหลัง */
        body::after {
            content: "";
            position: fixed;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            top: 10%;
            left: 8%;
            background: rgba(124, 58, 237, 0.22);
            animation: pulseGlow 5s ease-in-out infinite;
            box-shadow: 0 0 60px rgba(124, 58, 237, 0.2);
        }

        /* กรอบหลักที่ครอบหัวข้อ ฟอร์ม และข้อมูลนักศึกษา */
        .card {
            position: relative;
            z-index: 1;
            width: min(100%, 760px);
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(34, 211, 238, 0.22);
            border-radius: 28px;
            box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.12), 0 24px 55px rgba(2, 6, 23, 0.5), 0 0 26px rgba(124, 58, 237, 0.2);
            backdrop-filter: blur(12px);
            overflow: hidden;
            animation: fadeInUp 0.7s ease-out both;
        }

        /* แถบหัวข้อด้านบนของการ์ด */
        .header {
            padding: 26px 32px 18px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.16), rgba(34, 211, 238, 0.1));
            animation: fadeInUp 0.9s ease-out 0.1s both;
            box-shadow: inset 0 -1px 0 rgba(34, 211, 238, 0.1);
        }

        /* ขนาดและระยะห่างของหัวข้อหลัก */
        h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 2.8rem);
            letter-spacing: -0.05em;
        }

        /* แบ่งพื้นที่ด้านในเป็นคอลัมน์ฟอร์มและข้อมูล */
        .content {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 0;
        }

        /* รูปแบบพื้นฐานของกล่องฟอร์ม */
        form {
            padding: 28px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.42), rgba(15, 23, 42, 0.72));
            border: 1px solid rgba(34, 211, 238, 0.12);
            border-radius: 24px;
        }

        /* หัวข้อของฟอร์ม พร้อมข้อความกำกับที่เพิ่มด้วย ::after */
        .form-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 0 0 22px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            font-size: 1.2rem;
            letter-spacing: 0.04em;
            color: #dbeafe;
        }

        .form-header::after {
            content: "STUDENT INFO";
            font-size: 0.72rem;
            letter-spacing: 0.18em;
            color: rgba(34, 211, 238, 0.8);
            text-transform: uppercase;
        }

        /* จัดช่องกรอกข้อมูลเป็น 2 คอลัมน์ */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        /* กล่องของ label และ input แต่ละรายการ */
        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 14px 14px 12px;
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 16px;
            background: rgba(10, 15, 26, 0.52);
            animation: fadeInUp 0.75s ease-out both;
        }

        /* ให้ช่องที่มี class full กินพื้นที่ครบทุกคอลัมน์ */
        .field.full {
            grid-column: 1 / -1;
        }

        /* รูปแบบข้อความกำกับช่องกรอก */
        label {
            font-weight: 600;
            color: var(--muted);
            letter-spacing: 0.02em;
        }

        /* รูปแบบช่องกรอกข้อความและ textarea */
        input,
        /* textarea ใช้กรอกข้อความหลายบรรทัดและปรับความสูงได้ */
        textarea {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(15, 23, 42, 0.9);
            color: var(--text);
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 1rem;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        /* เน้นช่องที่กำลังพิมพ์อยู่ */
        input:focus,
        textarea:focus {
            outline: none;
            border-color: rgba(34, 211, 238, 0.7);
            box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.12);
            transform: translateY(-1px);
        }

        /* จัดปุ่มบันทึกให้อยู่ด้านขวาของฟอร์ม */
        .submit-wrap {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
        }

        /* รูปแบบปุ่มส่งฟอร์ม */
        button {
            border: none;
            border-radius: 14px;
            padding: 14px 22px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 18px 30px rgba(124, 58, 237, 0.35), 0 0 18px rgba(34, 211, 238, 0.18);
            cursor: pointer;
            transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
            animation: pulseGlow 3.2s ease-in-out infinite;
        }

        button:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 20px 34px rgba(124, 58, 237, 0.42), 0 0 20px rgba(34, 211, 238, 0.2);
            filter: brightness(1.06);
        }

        /* แผงด้านข้างสำหรับแสดงข้อมูลนักศึกษา */
        .info {
            padding: 32px 24px;
            border-left: 1px solid var(--line);
            background: rgba(9, 15, 27, 0.9);
        }

        /* กล่องแสดงข้อมูลนักศึกษาล่าสุด */
        .profile {
            padding: 18px 20px;
            background: rgba(124, 58, 237, 0.12);
            border-radius: 20px;
            border: 1px solid rgba(34, 211, 238, 0.25);
            box-shadow: inset 0 0 18px rgba(124, 58, 237, 0.18), 0 0 18px rgba(34, 211, 238, 0.12);
        }

        /* ระยะห่างของหัวข้อและข้อความในกล่องข้อมูล */
        .profile h2 {
            margin: 0 0 10px;
            font-size: 1.2rem;
        }

        .profile p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        /* ปุ่มลบข้อมูลที่ส่งล่าสุด */
        .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 16px;
            border: 1px solid rgba(248, 113, 113, 0.45);
            background: rgba(127, 29, 29, 0.18);
            color: #fecaca;
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .delete-btn:hover {
            transform: translateY(-1px);
            opacity: 0.96;
        }

        /* เปลี่ยนเป็นคอลัมน์เดียวเมื่อเปิดบนจอแคบ/มือถือ */
        @media (max-width: 720px) {
            .content {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .info {
                border-left: none;
                border-top: 1px solid var(--line);
            }

            form {
                padding: 22px 18px 28px;
            }
        }
    </style>
</head>
<body>
    <!-- การ์ดหลักที่รวมฟอร์มและข้อมูลนักศึกษา -->
    <div class="card">
        <div class="header">
            <h1>Student Dashboard</h1>
        </div>

        <div class="content">
            <!-- name ของ input คือ key ที่ PHP อ่านจาก $_POST; required ให้ browser บังคับกรอก -->
            <!-- method POST ส่งค่าฟอร์มกลับมายังไฟล์ PHP นี้ -->
            <form method="POST" action="">
                <h2 class="form-header">ข้อมูลนักศึกษา</h2>
                <div class="form-grid">
                    <div class="field">
                        <label for="stdid">รหัสนักศึกษา</label>
                        <!-- type number ให้กรอกตัวเลข -->
                        <input type="number" id="stdid" name="stdid" placeholder="กรอกรหัสนักศึกษา" required>
                    </div>

                    <div class="field">
                        <label for="stdname">ชื่อนักศึกษา</label>
                        <input type="text" id="stdname" name="stdname" placeholder="กรอกชื่อนักศึกษา" required>
                    </div>

                    <div class="field">
                        <label for="age">อายุ</label>
                        <!-- min/max จำกัดช่วงอายุที่ browser รับ -->
                        <input type="number" id="age" name="age" placeholder="กรอกอายุ" min="1" max="100" required>
                    </div>

                    <div class="field">
                        <label for="department">แผนกวิชา</label>
                        <input type="text" id="department" name="department" placeholder="กรอกแผนกวิชา" required>
                    </div>

                    <div class="field">
                        <label for="gpa">เกรดเฉลี่ย</label>
                        <!-- step กำหนดให้กรอก GPA ทีละ 0.01 ได้ -->
                        <input type="number" id="gpa" name="gpa" placeholder="กรอกเกรดเฉลี่ย" min="0" max="4" step="0.01" required>
                    </div>

                    <div class="field">
                        <label for="advisor">ครูที่ปรึกษา</label>
                        <input type="text" id="advisor" name="advisor" placeholder="กรอกชื่อครูที่ปรึกษา" required>
                    </div>

                    <div class="field">
                        <label for="phone">เบอร์</label>
                        <!-- จำกัดความยาวและรูปแบบเบอร์โทรเป็นตัวเลข 10 หลัก -->
                        <input type="tel" id="phone" name="phone" placeholder="กรอกเบอร์โทร" maxlength="10" pattern="[0-9]{10}" required>
                    </div>

                    <div class="field">
                        <label for="room">ห้อง</label>
                        <input type="text" id="room" name="room" placeholder="กรอกห้องเรียน" required>
                    </div>

                    <div class="field full">
                        <label for="address">ที่อยู่</label>
                        <!-- rows กำหนดจำนวนบรรทัดเริ่มต้นของช่องที่อยู่ -->
                        <textarea id="address" name="address" rows="3" placeholder="กรอกที่อยู่" required></textarea>
                    </div>
                </div>

                <div class="submit-wrap">
                    <!-- กดปุ่มนี้เพื่อส่งข้อมูลไปตรวจสอบด้านบนของไฟล์ -->
                    <button type="submit">บันทึกข้อมูล</button>
                </div>
            </form>

            <!-- แสดงข้อมูลที่ส่งล่าสุด หรือข้อมูลตัวอย่างเมื่อยังไม่มีข้อมูล -->
            <aside class="info">
                <div class="profile">
                    <h2>Latest profile</h2>
                    <p>
                        <?php
                            if ($submittedStudent instanceof Student) {
                                // htmlspecialchars ป้องกัน HTML ในข้อมูลถูกแปลเป็น tag; nl2br เปลี่ยน newline เป็น <br>
                                echo nl2br(htmlspecialchars($submittedStudent->getStudentInfo(), ENT_QUOTES, 'UTF-8'));
                            } else {
                                echo nl2br(htmlspecialchars($defaultStudent->getStudentInfo(), ENT_QUOTES, 'UTF-8'));
                            }
                        ?>
                    </p>

                    <?php if ($submittedStudent instanceof Student): ?>
                        <!-- ส่งคำสั่งลบกลับไปให้ PHP ล้างข้อมูลใน session -->
                        <form method="POST" action="">
                            <input type="hidden" name="delete_student" value="1">
                            <button type="submit" class="delete-btn">ลบข้อมูลที่เพิ่ม</button>
                        </form>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>