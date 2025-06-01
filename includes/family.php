<?php
session_start();
include('database.php');

if (!isset($db) || !$db instanceof mysqli) {
    die("Error: Database connection failed. Please check database.php configuration.");
}

if (strlen($_SESSION['detsuid']) == 0) {
    header('location:../logout.php');
    exit();
}

$uid = $_SESSION['detsuid'];
$success_message = '';
$error_message = '';

// Verify user exists
$query = "SELECT 1 FROM users WHERE id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) == 0) {
    $error_message = "Invalid user ID. Please log in again.";
    header('location:../logout.php');
    exit();
}

function generateSecurityCode($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

if (isset($_POST['create_family'])) {
    $family_name = mysqli_real_escape_string($db, $_POST['family_name']);
    $security_code = generateSecurityCode();
    
    $query = "INSERT INTO families (family_name, security_code, creator_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $family_name, $security_code, $uid);
    
    if (mysqli_stmt_execute($stmt)) {
        $family_id = mysqli_insert_id($db);
        
        // Check if user is already in user_families
        $query = "SELECT 1 FROM user_families WHERE user_id = ? AND family_id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            $query = "INSERT INTO user_families (user_id, family_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
            mysqli_stmt_execute($stmt);
        }
        
        $query = "UPDATE users SET family_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $family_id, $uid);
        mysqli_stmt_execute($stmt);
        
        $success_message = "Family created successfully! Your security code is: $security_code. Share this code with family members to join.";
    } else {
        $error_message = "Failed to create family. Please try again.";
    }
}

if (isset($_POST['join_family'])) {
    $family_id = (int)$_POST['family_id'];
    $entered_code = mysqli_real_escape_string($db, $_POST['security_code']);
    
    $query = "SELECT security_code FROM families WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    
    if ($row && $row['security_code'] === $entered_code) {
        // Check if user is already in user_families
        $query = "SELECT 1 FROM user_families WHERE user_id = ? AND family_id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            $query = "INSERT INTO user_families (user_id, family_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
            mysqli_stmt_execute($stmt);
        }
        
        $query = "UPDATE users SET family_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $family_id, $uid);
        mysqli_stmt_execute($stmt);
        $success_message = "Successfully joined the family!";
    } else {
        $error_message = "Invalid security code.";
    }
}

if (isset($_POST['delete_family'])) {
    $family_id = (int)$_POST['family_id'];
    
    $query = "SELECT creator_id FROM families WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    
    if ($row && $row['creator_id'] == $uid) {
        $query = "DELETE FROM user_families WHERE family_id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $family_id);
        mysqli_stmt_execute($stmt);
        
        $query = "UPDATE users SET family_id = NULL WHERE family_id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $family_id);
        mysqli_stmt_execute($stmt);
        
        $query = "DELETE FROM chat_messages WHERE family_id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $family_id);
        mysqli_stmt_execute($stmt);
        
        $query = "DELETE FROM families WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "i", $family_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Family deleted successfully!";
        } else {
            $error_message = "Failed to delete family. Please try again.";
        }
    } else {
        $error_message = "You are not authorized to delete this family.";
    }
}

if (isset($_POST['exit_family'])) {
    $family_id = (int)$_POST['family_id'];
    
    $query = "SELECT creator_id FROM families WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    
    if ($row && $row['creator_id'] == $uid) {
        $error_message = "Creators cannot exit their own family. Please delete the family instead.";
    } else {
        $query = "DELETE FROM user_families WHERE user_id = ? AND family_id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
        if (mysqli_stmt_execute($stmt)) {
            $query = "SELECT family_id FROM user_families WHERE user_id = ? LIMIT 1";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $new_family_id = mysqli_fetch_array($result)['family_id'] ?? NULL;
            
            $query = "UPDATE users SET family_id = ? WHERE id = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "ii", $new_family_id, $uid);
            mysqli_stmt_execute($stmt);
            
            unset($_SESSION['chat_security_verified']);
            
            $success_message = "Successfully exited the family!";
        } else {
            $error_message = "Failed to exit family. Please try again.";
        }
    }
}

if (isset($_POST['switch_family'])) {
    $family_id = (int)$_POST['family_id'];
    
    $query = "SELECT 1 FROM user_families WHERE user_id = ? AND family_id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ii", $uid, $family_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $query = "UPDATE users SET family_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $family_id, $uid);
        if (mysqli_stmt_execute($stmt)) {
            unset($_SESSION['chat_security_verified']);
            $success_message = "Switched to new family successfully!";
        } else {
            $error_message = "Failed to switch family. Please try again.";
        }
    } else {
        $error_message = "You are not a member of this family.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Family Management</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        .alert { margin-top: 20px; }
        .card { margin-bottom: 20px; }
        .security-code { font-family: monospace; }
        .copy-btn { cursor: pointer; }
        .table th, .table td { vertical-align: middle; }
        /* General container styling */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Sidebar styling */
.sidebar {
    background-color: #2c3e50;
    color: #fff;
    min-height: 100vh;
    transition: all 0.3s ease;
    width: 250px;
    position: fixed;
}

.sidebar.active {
    width: 80px;
}

.sidebar .logo-details {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar .logo_name {
    font-size: 24px;
    font-weight: 600;
}

.sidebar .nav-links {
    padding: 0;
    margin: 0;
    list-style: none;
}

.sidebar .nav-links li {
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

.sidebar .nav-links li:hover,
.sidebar .nav-links li.active {
    background-color: #34495e;
}

.sidebar .nav-links li a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 15px;
}

.sidebar .nav-links li a .links_name {
    font-size: 16px;
}

.sidebar.active .nav-links li a .links_name {
    display: none;
}

/* Home section styling */
.home-section {
    margin-left: 250px;
    padding: 20px;
    transition: all 0.3s ease;
}

.sidebar.active ~ .home-section {
    margin-left: 80px;
}

/* Navigation bar */
nav {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav .sidebar-button {
    display: flex;
    align-items: center;
    gap: 10px;
}

nav .sidebar-button .title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

nav .profile-details {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
}

nav .profile-details img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

nav .profile-details .admin_name {
    font-size: 16px;
    font-weight: 500;
    color: #333;
}

nav .profile-details .profile-options {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    list-style: none;
    padding: 10px 0;
    min-width: 150px;
}

nav .profile-details .profile-options.show {
    display: block;
}

nav .profile-options li a {
    display: block;
    padding: 10px 20px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
}

nav .profile-options li a:hover {
    background-color: #f1f1f1;
}

/* Card styling */
.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}

.card-header h4 {
    margin: 0;
    font-size: 20px;
    color: #333;
}

.card-body {
    padding: 20px;
}

/* Form styling */
.form-group {
    margin-bottom: 20px;
}

.form-control {
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 10px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

/* Button styling */
.btn {
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.1s ease;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #e0a800;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #c82333;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Table styling */
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.table td .security-code {
    background-color: #f1f1f1;
    padding: 2px 8px;
    border-radius: 4px;
}

.table td .copy-btn {
    color: #007bff;
    margin-left: 10px;
    transition: color 0.3s ease;
}

.table td .copy-btn:hover {
    color: #0056b3;
}

/* Alert styling */
.alert {
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sidebar {
        width: 80px;
    }

    .sidebar .logo_name,
    .sidebar .nav-links li a .links_name {
        display: none;
    }

    .home-section {
        margin-left: 80px;
    }

    nav .sidebar-button .title {
        font-size: 20px;
    }

    .table th,
    .table td {
        font-size: 12px;
        padding: 8px;
    }

    .btn {
        padding: 6px 12px;
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .container {
        padding: 10px;
    }

    .card-header h4 {
        font-size: 18px;
    }

    .form-control {
        font-size: 12px;
    }
}
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <i class='bx bx-album'></i>
            <span class="logo_name">Expenditure</span>
        </div>
        <ul class="nav-links">
            <li><a href="home.php"><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
            <li><a href="add-expenses.php"><i class='bx bx-box'></i><span class="links_name">Expenses</span></a></li>
            <li><a href="manage-expenses.php"><i class='bx bx-list-ul'></i><span class="links_name">Manage Expenses</span></a></li>
            <li><a href="lending.php"><i class='bx bx-money'></i><span class="links_name">Lending</span></a></li>
            <li><a href="manage-lending.php"><i class='bx bx-coin-stack'></i><span class="links_name">Manage Lending</span></a></li>
            <li><a href="analytics.php"><i class='bx bx-pie-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
            <li><a href="report.php"><i class='bx bx-file'></i><span class="links_name">Report</span></a></li>
            <li><a href="#" class="active"><i class='bx bx-group'></i><span class="links_name">Family</span></a></li>
            <li><a href="chat.php"><i class='bx bx-message'></i><span class="links_name">Chat</span></a></li>
            <li><a href="user_profile.php"><i class='bx bx-cog'></i><span class="links_name">Setting</span></a></li>
            <li class="log_out"><a href="../logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log Out</span></a></li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="title">Family Management</span>
            </div>
            <?php
            $ret = mysqli_query($db, "SELECT name FROM users WHERE id='$uid'");
            if ($ret && $row = mysqli_fetch_array($ret)) {
                $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            } else {
                $name = "Unknown User";
                $error_message = "Failed to load user profile.";
            }
            ?>
            <div class="profile-details">
                <img src="../images/maex.png" alt="">
                <span class="admin_name"><?php echo $name; ?></span>
                <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
                <ul class="profile-options" id='profile-options'>
                    <li><a href="../user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="home-content">
            <div class="container">
                <?php if ($success_message) { ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php } ?>
                <?php if ($error_message) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php } ?>

                <div class="card">
                    <div class="card-header"><h4>Your Families</h4></div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT f.id, f.family_name, f.security_code, f.creator_id, u.family_id AS active_family_id 
                                  FROM user_families uf 
                                  JOIN families f ON uf.family_id = f.id 
                                  JOIN users u ON u.id = uf.user_id 
                                  WHERE uf.user_id = ?";
                        $stmt = mysqli_prepare($db, $query);
                        mysqli_stmt_bind_param($stmt, "i", $uid);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table'>";
                            echo "<thead><tr><th>Family Name</th><th>Security Code</th><th>Status</th><th>Actions</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                $is_active = ($row['active_family_id'] == $row['id']) ? 'Active' : '';
                                $is_creator = ($row['creator_id'] == $uid);
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['family_name']) . "</td>";
                                echo "<td>" . ($is_creator ? "<span class='security-code'>" . htmlspecialchars($row['security_code']) . "</span> <i class='fas fa-copy copy-btn' onclick='copyCode(\"" . htmlspecialchars($row['security_code']) . "\")'></i>" : "-") . "</td>";
                                echo "<td>$is_active</td>";
                                echo "<td>";
                                if (!$is_creator) {
                                    echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to exit this family?\");'>
                                            <input type='hidden' name='family_id' value='{$row['id']}'>
                                            <button type='submit' name='exit_family' class='btn btn-warning btn-sm'>Exit</button>
                                          </form> ";
                                }
                                echo "<form method='POST' style='display:inline;'>
                                        <input type='hidden' name='family_id' value='{$row['id']}'>
                                        <button type='submit' name='switch_family' class='btn btn-primary btn-sm' " . ($is_active ? "disabled" : "") . ">Switch</button>
                                      </form>";
                                if ($is_creator) {
                                    echo " <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this family?\");'>
                                            <input type='hidden' name='family_id' value='{$row['id']}'>
                                            <button type='submit' name='delete_family' class='btn btn-danger btn-sm'>Delete</button>
                                          </form>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<p>You are not in any families yet.</p>";
                        }
                        ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4>Create New Family</h4></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Family Name</label>
                                <input type="text" name="family_name" class="form-control" placeholder="Family Name" required>
                            </div>
                            <button type="submit" name="create_family" class="btn btn-primary">Create Family</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4>Join Existing Family</h4></div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Select Family</label>
                                <select name="family_id" class="form-control" required>
                                    <option value="" selected disabled>Choose Family</option>
                                    <?php
                                    $query = "SELECT * FROM families";
                                    $result = mysqli_query($db, $query);
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo "<option value='{$row['id']}'>" . htmlspecialchars($row['family_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Security Code</label>
                                <input type="text" name="security_code" class="form-control" placeholder="Enter Security Code" required>
                            </div>
                            <button type="submit" name="join_family" class="btn btn-primary">Join Family</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        sidebarBtn.onclick = function() {
            sidebar.classList.toggle("active");
            if (sidebar.classList.contains("active")) {
                sidebarBtn.classList.replace("bx-menu", "bx-x");
            } else {
                sidebarBtn.classList.replace("bx-x", "bx-menu");
            }
        }

        const toggleButton = document.getElementById('profile-options-toggle');
        const profileOptions = document.getElementById('profile-options');
        toggleButton.addEventListener('click', () => {
            profileOptions.classList.toggle('show');
        });

        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Security code copied to clipboard!');
            }).catch(() => {
                alert('Failed to copy code.');
            });
        }
    </script>
</body>
</html>