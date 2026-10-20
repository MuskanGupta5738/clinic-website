
<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Please submit the appointment form.");
}

require_once "db.php";

$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$department = trim($_POST["department"] ?? "");
$appointment_date = trim($_POST["appointment_date"] ?? "");
$message = trim($_POST["message"] ?? "");

if (
    $full_name === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $phone === "" ||
    $department === "" ||
    $appointment_date === ""
) {
    exit("Please enter valid appointment details.");
}

if (
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointment_date) ||
    !checkdate(
        (int)substr($appointment_date, 5, 2),
        (int)substr($appointment_date, 8, 2),
        (int)substr($appointment_date, 0, 4)
    )
) {
    exit("Please select a valid appointment date.");
}

// date_default_timezone_set('Asia/Kolkata');

// $today = date('Y-m-d');

// if ($appointment_date < $today) {
//     exit("Past dates are not allowed. Please select today or a future date.");
// }


$sql = "INSERT INTO appointments
        (full_name, email, phone, department, appointment_date, message)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Unable to process the appointment right now.");
}

$stmt->bind_param(
    "ssssss",
    $full_name,
    $email,
    $phone,
    $department,
    $appointment_date,
    $message
);


if ($stmt->execute()) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Appointment Confirmed | MediCare</title>
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                font-family: Arial, sans-serif;
                background: #f4effb;
                color: #302044;
            }

            .success-card {
                width: 100%;
                max-width: 520px;
                padding: 45px 30px;
                text-align: center;
                background: #ffffff;
                border-radius: 22px;
                box-shadow: 0 15px 45px rgba(94, 58, 130, 0.12);
            }

            .check-icon {
                width: 78px;
                height: 78px;
                margin: 0 auto 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: #eee2fb;
                color: #9164c5;
                font-size: 42px;
            }

            h1 {
                margin-bottom: 12px;
                font-size: 30px;
            }

            p {
                color: #6d627b;
                line-height: 1.7;
            }

            .home-btn {
                display: inline-block;
                margin-top: 22px;
                padding: 14px 25px;
                border-radius: 10px;
                background: #9164c5;
                color: #ffffff;
                text-decoration: none;
                font-weight: bold;
            }

            .home-btn:hover {
                background: #7950a8;
            }
        </style>
    </head>
    <body>
        <main class="success-card">
            <div class="check-icon">&#10003;</div>
            <h1>Appointment Submitted!</h1>
            <p>Thank you, ' . htmlspecialchars($full_name, ENT_QUOTES, "UTF-8") . '!</p>
            <p>Your appointment request has been saved successfully. Our clinic team will review your request.</p>
            <a class="home-btn" href="index.html">Back to Home</a>
        </main>
    </body>
    </html>';
} else {
    echo "Unable to save the appointment. Please try again.";
}
