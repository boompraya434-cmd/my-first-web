<?php
    // รับรายการจาก controller; ถ้าไม่มีข้อมูลให้ใช้รายการว่างแทน
    $students = $student ?? [];

    // Escape ข้อความก่อนใส่ HTML เพื่อไม่ให้ค่าจากฐานข้อมูลกลายเป็น markup
    $escape = static function ($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    };

    // รวมจำนวนนักศึกษาตาม major เพื่อแสดงรายชื่อแผนกแบบไม่ซ้ำกัน
    $majorCounts = [];
    foreach ($students as $row) {
        $majorName = trim((string) ($row['major'] ?? ''));
        if ($majorName === '') {
            $majorName = 'ไม่ระบุแผนก';
        }

        $majorCounts[$majorName] = ($majorCounts[$majorName] ?? 0) + 1;
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อนักศึกษา</title>
    <style>
        :root {
            color-scheme: light;
            --page: #f3f6fb;
            --surface: #ffffff;
            --ink: #17233b;
            --muted: #71809a;
            --line: #e8edf5;
            --brand: #4f46e5;
            --brand-soft: #eef0ff;
            --teal: #0f9f92;
            --shadow: 0 18px 50px rgba(34, 49, 84, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(ellipse at 10% 0%, rgba(79, 70, 229, 0.09), transparent 34rem),
                var(--page);
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .page-shell {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
            padding: 38px 0 56px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 48px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--ink);
            font-size: 15px;
            font-weight: 750;
            letter-spacing: -0.02em;
        }

        .brand-mark {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border-radius: 13px;
            background: var(--brand);
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.25);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }

        .topbar-label {
            color: var(--muted);
            font-size: 13px;
        }

        .intro {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--brand);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            letter-spacing: -0.045em;
            line-height: 1.15;
        }

        .intro-copy {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .record-count {
            min-width: 154px;
            padding: 16px 20px;
            border: 1px solid rgba(79, 70, 229, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.78);
            text-align: right;
        }

        .record-count strong {
            display: block;
            color: var(--brand);
            font-size: 26px;
            letter-spacing: -0.04em;
        }

        .record-count span {
            color: var(--muted);
            font-size: 12px;
        }

        .data-card {
            overflow: hidden;
            border: 1px solid rgba(224, 231, 242, 0.9);
            border-radius: 20px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .section-stack {
            display: grid;
            gap: 26px;
        }

        .card-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 26px;
            border-bottom: 1px solid var(--line);
        }

        .card-heading h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 750;
            letter-spacing: -0.02em;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 12px;
        }

        .status::before {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--teal);
            content: "";
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            white-space: nowrap;
        }

        th {
            padding: 14px 26px;
            background: #f8f9fc;
            color: #8390a6;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        td {
            padding: 17px 26px;
            border-top: 1px solid var(--line);
            color: #4d5b73;
            font-size: 14px;
        }

        tbody tr {
            transition: background-color 160ms ease;
        }

        tbody tr:hover {
            background: #fafbff;
        }

        td:first-child {
            color: #97a2b5;
            font-size: 12px;
            font-weight: 700;
        }

        .student-name {
            color: var(--ink);
            font-weight: 650;
        }

        .major-badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 11px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: #5146cc;
            font-size: 12px;
            font-weight: 650;
        }

        .empty-state {
            padding: 56px 24px;
            color: var(--muted);
            text-align: center;
            white-space: normal;
        }

        .empty-state strong {
            display: block;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 15px;
        }

        .footer-note {
            margin: 18px 4px 0;
            color: #8995a9;
            font-size: 12px;
        }

        @media (max-width: 640px) {
            .page-shell {
                width: min(100% - 32px, 1120px);
                padding-top: 24px;
            }

            .topbar {
                margin-bottom: 36px;
            }

            .topbar-label {
                display: none;
            }

            .intro {
                align-items: flex-start;
                flex-direction: column;
                gap: 18px;
            }

            .record-count {
                display: flex;
                min-width: 0;
                align-items: baseline;
                gap: 9px;
                padding: 10px 14px;
                text-align: left;
            }

            .record-count strong {
                font-size: 20px;
            }

            .card-heading {
                padding: 18px;
            }

            th,
            td {
                padding-right: 18px;
                padding-left: 18px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="page-shell">
        <header class="topbar">
            <div class="brand">
                <span class="brand-mark" aria-hidden="true">ST</span>
                <span>Student Portal</span>
            </div>
            <span class="topbar-label">ระบบจัดการข้อมูลนักศึกษา</span>
        </header>

        <section class="intro" aria-labelledby="page-title">
            <div>
                <p class="eyebrow">Student directory</p>
                <h1 id="page-title">รายชื่อนักศึกษา</h1>
                <p class="intro-copy">ดูข้อมูลนักศึกษา สาขาวิชา และช่องทางติดต่อได้ในที่เดียว</p>
            </div>
            <div class="record-count" aria-label="จำนวนนักศึกษาทั้งหมด">
                <strong><?= count($students) ?></strong>
                <span>นักศึกษาทั้งหมด</span>
            </div>
        </section>

        <div class="section-stack">
            <section class="data-card" aria-labelledby="students-title">
                <div class="card-heading">
                    <h2 id="students-title">ข้อมูลรวมของนักศึกษา</h2>
                    <span class="status">รายชื่อนักศึกษาทั้งหมด</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">ลำดับ</th>
                                <th scope="col">รหัสนักศึกษา</th>
                                <th scope="col">ชื่อ-นามสกุล</th>
                                <th scope="col">เบอร์โทรศัพท์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($students === []): ?>
                                <tr>
                                    <td class="empty-state" colspan="4">
                                        <strong>ยังไม่มีข้อมูลนักศึกษา</strong>
                                        เมื่อมีข้อมูลในระบบ รายชื่อจะแสดงที่นี่
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $index => $row): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $escape($row['id'] ?? '') ?></td>
                                        <td class="student-name"><?= $escape($row['stdname'] ?? '') ?></td>
                                        <td><?= $escape($row['tel'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="data-card" aria-labelledby="majors-title">
                <div class="card-heading">
                    <h2 id="majors-title">ข้อมูลแผนก / สาขาวิชา</h2>
                    <span class="status"><?= count($majorCounts) ?> แผนก</span>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">ชื่อแผนก / สาขาวิชา</th>
                                <th scope="col">จำนวนนักศึกษา</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($majorCounts === []): ?>
                                <tr>
                                    <td class="empty-state" colspan="2">
                                        <strong>ยังไม่มีข้อมูลแผนก</strong>
                                        เมื่อมีข้อมูลนักศึกษา แผนกจะแสดงที่นี่
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($majorCounts as $majorName => $count): ?>
                                    <tr>
                                        <td><span class="major-badge"><?= $escape($majorName) ?></span></td>
                                        <td><?= $count ?> คน</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <p class="footer-note">ข้อมูลแสดงตามรายการที่มีอยู่ในระบบ</p>
    </main>
</body>
</html>