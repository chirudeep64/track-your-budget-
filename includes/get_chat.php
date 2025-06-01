<?php
session_start();
include('database.php');

if (!isset($_SESSION['detsuid']) || strlen($_SESSION['detsuid']) == 0) {
    echo '<p class="text-danger">Please log in to view chat.</p>';
    exit();
}

$uid = $_SESSION['detsuid'];
$family_id = isset($_GET['family_id']) ? (int)$_GET['family_id'] : 0;

if (!$family_id) {
    echo '<p class="text-danger">No family selected.</p>';
    exit();
}

// Verify user is part of the family
$query = "SELECT 1 FROM users WHERE id = ? AND family_id = ?";
$stmt = mysqli_prepare($db, $query);
if (!$stmt) {
    echo '<p class="text-danger">Database error: ' . mysqli_error($db) . '</p>';
    exit();
}
mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) == 0) {
    echo '<p class="text-danger">You are not a member of this family.</p>';
    exit();
}

// Fetch messages
$query = "SELECT cm.*, u.name FROM chat_messages cm JOIN users u ON cm.user_id = u.id WHERE cm.family_id = ? ORDER BY cm.created_at ASC";
$stmt = mysqli_prepare($db, $query);
if (!$stmt) {
    echo '<p class="text-danger">Database error: ' . mysqli_error($db) . '</p>';
    exit();
}
mysqli_stmt_bind_param($stmt, "i", $family_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    echo '<p class="text-danger">Query error: ' . mysqli_error($db) . '</p>';
    exit();
}

while ($row = mysqli_fetch_array($result)) {
    $is_sent = ($row['user_id'] == $uid);
    $class = $is_sent ? 'sent' : 'received';
    echo '<div class="message ' . $class . '">';
    echo '<div class="message-content">';
    echo '<div class="sender">' . htmlspecialchars($row['name']) . '</div>';
    if ($row['message']) {
        echo '<p>' . htmlspecialchars($row['message']) . '</p>';
    }
    if ($row['image_path']) {
        echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Chat Image">';
    }
    echo '<div class="timestamp">' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</div>';
    echo '</div></div>';
}
?>