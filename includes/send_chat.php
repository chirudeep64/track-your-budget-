<?php
session_start();
include('database.php');

if (!isset($_SESSION['detsuid']) || strlen($_SESSION['detsuid']) == 0) {
    echo "Please log in.";
    exit();
}

$uid = $_SESSION['detsuid'];
$family_id = isset($_POST['family_id']) ? (int)$_POST['family_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$image_path = null;

if (!$family_id) {
    echo "No family selected.";
    exit();
}

// Verify user is part of the family
$query = "SELECT 1 FROM users WHERE id = ? AND family_id = ?";
$stmt = mysqli_prepare($db, $query);
if (!$stmt) {
    echo "Database error: " . mysqli_error($db);
    exit();
}
mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) == 0) {
    echo "You are not a member of this family.";
    exit();
}

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = ['image/png', 'image/jpeg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $file_type = $_FILES['image']['type'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('chat_') . '.' . $file_ext;
    $upload_dir = '../Uploads/chat/';
    $upload_path = $upload_dir . $file_name;

    if (!in_array($file_type, $allowed)) {
        echo "Invalid file type. Only PNG and JPEG are allowed.";
        exit();
    }
    if ($file_size > $max_size) {
        echo "File size exceeds 5MB limit.";
        exit();
    }
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            echo "Failed to create upload directory.";
            exit();
        }
    }
    if (!move_uploaded_file($file_tmp, $upload_path)) {
        echo "Failed to upload image.";
        exit();
    }
    $image_path = $upload_path;
}

if (!$message && !$image_path) {
    echo "Message or image required.";
    exit();
}

// Insert message
$query = "INSERT INTO chat_messages (family_id, user_id, message, image_path) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($db, $query);
if (!$stmt) {
    echo "Database error: " . mysqli_error($db);
    exit();
}
mysqli_stmt_bind_param($stmt, "iiss", $family_id, $uid, $message, $image_path);
if (mysqli_stmt_execute($stmt)) {
    echo "Message sent";
} else {
    echo "Failed to send message: " . mysqli_error($db);
}
?>