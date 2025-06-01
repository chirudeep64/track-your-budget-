<?php
session_start();
include('database.php');

if (!isset($db) || !$db instanceof mysqli) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

if (!isset($_SESSION['detsuid']) || strlen($_SESSION['detsuid']) == 0) {
    http_response_code(401);
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

if (!isset($_SESSION['chat_security_verified']) || $_SESSION['chat_security_verified'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Security code not verified']);
    exit();
}

$upload_dir = '../Uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$photo_url = null;
if (isset($_FILES['photo-upload']) && $_FILES['photo-upload']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['photo-upload'];
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024;

    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = $upload_dir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $photo_url = "Uploads/$filename";
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload photo']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type or size']);
        exit();
    }
}

$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($family_id && ($message || $photo_url)) {
    $query = "INSERT INTO chat_messages (family_id, user_id, message, photo_url, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $family_id, $uid, $message, $photo_url);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No valid family_id or content']);
}
?>