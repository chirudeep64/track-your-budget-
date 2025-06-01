<?php
session_start();
include('database.php');

if (!isset($_SESSION['detsuid'])) {
    header('location:logout.php');
    exit();
}

if (isset($_POST['update_user'])) {
    $userid = $_SESSION['detsuid'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Username is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }

    // Handle profile image upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Invalid image format. Only JPEG, PNG, and GIF are allowed.";
        }
        if ($file['size'] > $max_size) {
            $errors[] = "Image size exceeds 5MB.";
        }

        if (empty($errors)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userid . '_' . time() . '.' . $ext;
            $upload_dir = 'images/';
            $upload_path = $upload_dir . $filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $profile_image = $upload_path;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Update database if no errors
    if (empty($errors)) {
        $query = "UPDATE users SET name = ?, email = ?, phone = ?";
        $params = [$name, $email, $phone];
        $types = "sss";

        if ($profile_image) {
            $query .= ", profile_image = ?";
            $params[] = $profile_image;
            $types .= "s";
        }

        $query .= " WHERE id = ?";
        $params[] = $userid;
        $types .= "i";

        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Profile updated successfully";
            header('location:user_profile.php');
        } else {
            $errors[] = "Failed to update profile: " . mysqli_error($db);
        }
        mysqli_stmt_close($stmt);
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header('location:user_profile.php');
    }
}
exit();
?>