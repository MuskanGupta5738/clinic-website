
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
    echo "Appointment submitted successfully!";
} else {
    echo "Unable to save the appointment. Please try again.";
}

$stmt->close();
$conn->close();
?>
