<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System - Home</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body, html {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/home.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        /* Lighter overlay — no heavy darkness */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.38);   /* ← reduced from 0.40 */
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        h1 {
            text-align: center;
            font-size: 4rem;
            margin-bottom: 30px;
            text-shadow: 3px 3px 12px rgba(0,0,0,0.9);
            color: #00ffff;
            letter-spacing: 2px;
        }

        .menu {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 40px;                    /* ← original spacious padding back */
            border-radius: 20px;
            max-width: 1150px;
            margin: 0 auto;
            border: 1px solid rgba(255,255,255,0.25);
            
            /* Softer, lighter shadow — no black feel */
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22),
                        0 6px 12px rgba(0, 0, 0, 0.18),
                        inset 0 0 20px rgba(255,255,255,0.08);
            line-height: 2.4;                 /* ← original spacious line height */
        }

        .menu strong {
            color: #00ffea;
            font-size: 1.5rem;
            display: block;
            margin: 25px 0 12px 0;            /* ← original spacing restored */
            text-shadow: 1px 1px 8px rgba(0,0,0,0.8);
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            margin: 0 14px;
            padding: 10px 16px;
            border-radius: 12px;
            transition: all 0.35s ease;
            display: inline-block;
        }

        .menu a:hover {
            background: rgba(0, 255, 255, 0.35);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 255, 255, 0.3);
        }

        .footer-text {
            text-align: center;
            font-size: 1.3rem;
            margin-top: 40px;
            text-shadow: 2px 2px 10px black;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <div>
        <h1>Hospital Management System</h1>

        <div class="menu">
            <strong>People & Structure</strong><br>
            <a href="patients.php">Patients</a> |
            <a href="doctors.php">Doctors</a> |
            <a href="staff.php">Staff</a> |
            <a href="departments.php">Departments</a> |
            <a href="wards.php">Wards</a> |
            <a href="rooms.php">Rooms</a><br><br>

            <strong>Medical Services</strong><br>
            <a href="appointments.php">Appointments</a> |
            <a href="admissions.php">Admissions</a> |
            <a href="treatments.php">Treatments</a> |
            <a href="prescriptions.php">Prescriptions</a><br><br>

            <strong>Lab & Finance</strong><br>
            <a href="lab_tests.php">Lab Tests</a> |
            <a href="lab_reports.php">Lab Reports</a> |
            <a href="medicines.php">Medicines</a> |
            <a href="invoices.php">Invoices</a>
        </div>

        <div class="footer-text">
            Welcome! Use the menu above to manage all parts of the hospital.
        </div>
    </div>

    <div class="footer-text">
        © 2025 Hospital Management System • All Rights Reserved
    </div>
</div>

</body>
</html>