<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE); // Show all errors except notices
include('database.php');

if (!isset($_SESSION['detsuid']) || empty($_SESSION['detsuid'])) {
    header('location:logout.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --background: #f4f7fa;
            --card-bg: #ffffff;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background);
            color: #333;
            line-height: 1.6;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: var(--card-bg);
            box-shadow: var(--shadow);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s ease;
            z-index: 1100;
            overflow-y: auto;
        }

        .sidebar.active {
            width: 80px;
        }

        .sidebar .logo-details {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(90deg, var(--primary), #0056b3);
            color: #fff;
        }

        .sidebar .logo-details i {
            font-size: 28px;
        }

        .sidebar .logo-details .logo_name {
            font-size: 20px;
            font-weight: 600;
            transition: opacity 0.3s ease;
        }

        .sidebar.active .logo-details .logo_name {
            opacity: 0;
        }

        .nav-links {
            padding: 10px 0;
            list-style: none;
        }

        .nav-links li {
            margin: 5px 0;
        }

        .nav-links li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: background 0.3s, color 0.3s, padding-left 0.3s;
            border-radius: 0 12px 12px 0;
        }

        .nav-links li a i {
            font-size: 20px;
            min-width: 30px;
            text-align: center;
        }

        .nav-links li a .links_name {
            font-size: 15px;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav-links li a:hover, .nav-links li a.active {
            background: var(--primary);
            color: #fff;
            padding-left: 25px;
        }

        .nav-links li a:hover i, .nav-links li a.active i {
            color: #fff;
        }

        .sidebar.active .nav-links li a .links_name {
            opacity: 0;
        }

        .sidebar.active .nav-links li a {
            justify-content: center;
        }

        .nav-links li.log_out {
            margin-top: auto;
        }

        .nav-links li.log_out a {
            border-top: 1px solid #e0e0e0;
        }

        /* Main Content */
        .home-section {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.active ~ .home-section {
            margin-left: 80px;
        }

        /* Navbar Styling */
        nav {
            background: var(--card-bg);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-button {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .sidebar-button .sidebarBtn {
            font-size: 24px;
            color: var(--primary);
            transition: transform 0.2s, color 0.2s;
        }

        .sidebar-button .sidebarBtn:hover {
            color: #0056b3;
            transform: scale(1.1);
        }

        .sidebar-button .dashboard {
            font-size: 20px;
            font-weight: 500;
            color: #333;
        }

        .profile-details {
            position: relative;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .profile-details:hover {
            background: #e9ecef;
        }

        .profile-details img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            transition: transform 0.2s;
        }

        .profile-details:hover img {
            transform: scale(1.05);
        }

        .profile-details .admin_name {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .profile-details .bx-chevron-down {
            font-size: 20px;
            color: var(--secondary);
            transition: transform 0.3s ease;
        }

        .profile-details:hover .bx-chevron-down {
            transform: rotate(180deg);
        }

        .profile-options {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            min-width: 180px;
            z-index: 100;
            margin-top: 5px;
            padding: 10px 0;
        }

        .profile-options.show {
            display: block;
        }

        .profile-options li {
            list-style: none;
        }

        .profile-options li a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #333;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
        }

        .profile-options li a i {
            margin-right: 10px;
            font-size: 16px;
        }

        .profile-options li a:hover {
            background: var(--primary);
            color: #fff;
        }

        /* Profile Tab Navigation */
        .profile-tab-nav {
            min-width: 280px;
            background: var(--card-bg);
            border-right: 1px solid #e0e0e0;
            border-radius: var(--border-radius) 0 0 var(--border-radius);
        }

        .tab-content {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            box-shadow: var(--shadow);
        }

        .nav-pills .nav-link {
            padding: 15px 25px;
            color: #333;
            font-weight: 500;
            border-bottom: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(90deg, var(--primary), #0056b3);
            color: #fff;
            border-left: 4px solid #003087;
        }

        .nav-pills .nav-link:hover {
            background: #e9ecef;
            color: var(--primary);
        }

        .img-circle {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        #profile-image-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: var(--shadow);
            object-fit: cover;
            transition: transform 0.3s ease, opacity 0.3s;
        }

        .img-circle:hover #profile-image-preview {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        .img-circle:hover .overlay {
            opacity: 1;
        }

        .overlay i {
            color: #fff;
            font-size: 28px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 12px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), #0056b3);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-light {
            border: 1px solid #ced4da;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background 0.3s, color 0.3s;
        }

        .btn-light:hover {
            background: #e9ecef;
            color: var(--primary);
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .sidebar {
                width: 80px;
            }

            .sidebar .logo-details .logo_name {
                opacity: 0;
            }

            .nav-links li a .links_name {
                opacity: 0;
            }

            .nav-links li a {
                justify-content: center;
            }

            .home-section {
                margin-left: 80px;
            }

            .sidebar.active {
                width: 80px;
            }

            .sidebar.active ~ .home-section {
                margin-left: 80px;
            }

            .profile-tab-nav {
                min-width: 100%;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                border-radius: var(--border-radius) var(--border-radius) 0 0;
            }

            .tab-content {
                padding: 20px;
                border-radius: 0 0 var(--border-radius) var(--border-radius);
            }

            #profile-image-preview {
                width: 100px;
                height: 100px;
            }

            nav {
                padding: 10px 15px;
                flex-direction: column;
                gap: 10px;
            }

            .sidebar-button {
                justify-content: center;
            }

            .profile-details {
                justify-content: center;
            }

            .profile-options {
                right: 50%;
                transform: translateX(50%);
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 60px;
            }

            .home-section {
                margin-left: 60px;
            }

            .sidebar.active {
                width: 60px;
            }

            .sidebar.active ~ .home-section {
                margin-left: 60px;
            }

            .sidebar .logo-details i {
                font-size: 24px;
            }

            .nav-links li a i {
                font-size: 18px;
            }

            .sidebar-button .dashboard {
                font-size: 18px;
            }

            .profile-details .admin_name {
                font-size: 14px;
            }

            .profile-details img {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <i class="bx bx-album"></i>
            <span class="logo_name">Expenditure</span>
        </div>
        <ul class="nav-links">
            <li><a href="home.php"><i class="bx bx-grid-alt"></i><span class="links_name">Dashboard</span></a></li>
            <li><a href="add-expenses.php"><i class="bx bx-box"></i><span class="links_name">Expenses</span></a></li>
            <li><a href="manage-expenses.php"><i class="bx bx-list-ul"></i><span class="links_name">Manage List</span></a></li>
            <li><a href="lending.php"><i class="bx bx-money"></i><span class="links_name">Lending</span></a></li>
            <li><a href="manage-lending.php"><i class="bx bx-coin-stack"></i><span class="links_name">Manage Lending</span></a></li>
            <li><a href="analytics.php"><i class="bx bx-pie-chart-alt-2"></i><span class="links_name">Analytics</span></a></li>
            <li><a href="report.php"><i class="bx bx-file"></i><span class="links_name">Report</span></a></li>
            <li><a href="#" class="active"><i class="bx bx-cog"></i><span class="links_name">Setting</span></a></li>
            <li class="log_out"><a href="logout.php"><i class="bx bx-log-out"></i><span class="links_name">Log out</span></a></li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class="bx bx-menu sidebarBtn"></i>
                <span class="dashboard">Setting</span>
            </div>
            <?php
            $uid = $_SESSION['detsuid'];
            $stmt = mysqli_prepare($db, "SELECT name, email, phone, profile_image, created_at FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $name = $row['name'];
                $email = $row['email'];
                $phone = $row['phone'];
                $profile_image = $row['profile_image'] ?: 'images/maex.png';
            } else {
                $name = 'Guest';
                $email = '';
                $phone = '';
                $profile_image = 'images/maex.png';
            }
            mysqli_stmt_close($stmt);
            ?>
            <div class="profile-details">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image">
                <span class="admin_name"><?php echo htmlspecialchars($name); ?></span>
                <i class="bx bx-chevron-down" id="profile-options-toggle"></i>
                <ul class="profile-options" id="profile-options">
                    <li><a href="#"><i class="fas fa-user-circle"></i> User Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>

        <div class="container mx-auto">
            <div class="bg-white shadow rounded-lg d-block d-sm-flex">
                <div class="profile-tab-nav border-right">
                    <div class="p-4">
                        <center>
                            <label for="profile-image-input">
                                <div class="img-circle text-center mb-3">
                                    <img id="profile-image-preview" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" class="shadow">
                                    <div class="overlay">
                                        <i class="fas fa-camera fa-lg"></i>
                                    </div>
                                </div>
                            </label>
                            <input type="file" id="profile-image-input" name="profile_image" accept="image/*" style="display: none;">
                        </center>
                        <h4 class="text-center"><?php echo htmlspecialchars($name); ?></h4>
                    </div>
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="account-tab" data-toggle="pill" href="#account" role="tab" aria-controls="account" aria-selected="true">
                            <i class="fa fa-home text-center mr-1"></i> Account
                        </a>
                        <a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false">
                            <i class="fa fa-key text-center mr-1"></i> Password
                        </a>
                    </div>
                </div>
                <div class="tab-content p-4 p-md-5" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab">
                        <h3 class="mb-4">Account Settings</h3>
                        <?php
                        if (isset($_SESSION['profile_errors'])) {
                            echo '<div class="alert alert-danger">';
                            foreach ($_SESSION['profile_errors'] as $error) {
                                echo '<p>' . htmlspecialchars($error) . '</p>';
                            }
                            echo '</div>';
                            unset($_SESSION['profile_errors']);
                        }
                        if (isset($_SESSION['success_message'])) {
                            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                            unset($_SESSION['success_message']);
                        }
                        ?>
                        <form id="account-form" method="POST" action="update_user.php" enctype="multipart/form-data">
                            <input type="file" name="profile_image" id="hidden-profile-image" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Registered Date</label>
                                        <input type="date" class="form-control" value="<?php echo isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : ''; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" name="update_user">Update</button>
                                <button type="button" class="btn btn-light" onclick="location.href='user_profile.php'">Cancel</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <h3 class="mb-4">Password Settings</h3>
                        <form id="password-form" method="POST" action="update_password.php">
                            <?php
                            if (isset($_SESSION['password_errors'])) {
                                echo '<div class="alert alert-danger">';
                                foreach ($_SESSION['password_errors'] as $error) {
                                    echo '<p>' . htmlspecialchars($error) . '</p>';
                                }
                                echo '</div>';
                                unset($_SESSION['password_errors']);
                            }
                            if (isset($_SESSION['password_message'])) {
                                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['password_message']) . '</div>';
                                unset($_SESSION['password_message']);
                            }
                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Old Password</label>
                                        <input type="password" class="form-control" name="old_password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary" type="submit" name="update_password">Update</button>
                                <button class="btn btn-light" type="reset">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            // Sidebar toggle
            let sidebar = document.querySelector(".sidebar");
            let sidebarBtn = document.querySelector(".sidebarBtn");
            sidebarBtn.onclick = function() {
                sidebar.classList.toggle("active");
                if (sidebar.classList.contains("active")) {
                    sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
                } else {
                    sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
                }
            };

            // Profile options toggle
            const toggleButton = document.getElementById("profile-options-toggle");
            const profileOptions = document.getElementById("profile-options");
            toggleButton.addEventListener("click", () => {
                profileOptions.classList.toggle("show");
            });

            // Profile image preview and form sync
            $("#profile-image-input").change(function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $("#profile-image-preview").attr("src", e.target.result);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        $("#hidden-profile-image")[0].files = dataTransfer.files;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Close profile options when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest(".profile-details").length) {
                    profileOptions.classList.remove("show");
                }
            });
        });
    </script>
</body>
</html>