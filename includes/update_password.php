<?php
session_start();
include('database.php');

if (!isset($_SESSION['detsuid'])) {
    header('location:logout.php');
    exit();
}

if (isset($_POST['update_password'])) {
    $userid = $_SESSION['detsuid'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    // Validate inputs
    if (empty($old_password)) {
        $errors[] = "Please enter your old password";
    }
    if (empty($new_password)) {
        $errors[] = "Please enter a new password";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Your new password must be at least 8 characters long";
    }
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your new password";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Your new passwords do not match";
    }

    // Verify old password
    $stmt = mysqli_prepare($db, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!password_verify($old_password, $row['password'])) {
        $errors[] = "Your old password is incorrect";
    }
    mysqli_stmt_close($stmt);

    // Update password if no errors
    if (empty($errors)) {
        $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($db, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $password_hashed, $userid);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['password_message'] = "Password updated successfully";
        } else {
            $errors[] = "Failed to update password: " . mysqli_error($db);
        }
        mysqli_stmt_close($stmt);
    }

    if (!empty($errors)) {
        $_SESSION['password_errors'] = $errors;
    }
    header('location:user_profile.php');
}
exit();
?>