<?php
session_start();
include('database.php');

if (!isset($_SESSION['detsuid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (isset($_POST['add-category-submit'])) {
    $userid = $_SESSION['detsuid'];
    $categoryName = trim($_POST['category-name']);

    // Validate input
    if (empty($categoryName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        exit();
    }

    // Use prepared statement to insert category
    $query = "INSERT INTO tblcategory (UserId, CategoryName) VALUES (?, ?)";
    $stmt = mysqli_prepare($db, $query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($db)]);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "is", $userid, $categoryName);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add category: ' . mysqli_error($db)]);
    }

    mysqli_stmt_close($stmt);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>