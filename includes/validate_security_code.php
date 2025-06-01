<?php
session_start();
include('database.php');

if (!isset($db) || !$db instanceof mysqli) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

if (!isset($_SESSION['detsuid']) || strlen($_SESSION['detsuid']) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$uid = $_SESSION['detsuid'];
$query = "SELECT family_id FROM users WHERE id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$family_id = mysqli_fetch_array($result)['family_id'];

if (!$family_id) {
    echo json_encode(['status' => 'error', 'message' => 'Join a family first']);
    exit();
}

if (isset($_POST['code'])) {
    $code = trim($_POST['code']);
    $query = "SELECT security_code FROM families WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);

    if ($row && $row['security_code'] === $code) {
        $_SESSION['chat_security_verified'] = true;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid security code']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No code provided']);
}
?>