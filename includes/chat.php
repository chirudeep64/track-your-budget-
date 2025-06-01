<?php
session_start();
include('database.php');
if (strlen($_SESSION['detsuid']) == 0) {
    header('location:logout.php');
    exit();
}

$uid = $_SESSION['detsuid'];

// Verify user and family
$query = "SELECT name, family_id, profile_image FROM users WHERE id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);
if (!$row) {
    header('location:logout.php');
    exit();
}
$name = htmlspecialchars($row['name']);
$family_id = $row['family_id'];
$profile_path = 'Uploads/' . $row['profile_image'];
$profile_image = ($row['profile_image'] && file_exists($profile_path)) ? $profile_path : 'images/maex.png';

if (!$family_id) {
    $error_message = "You must join a family to access the chat.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Family Chat</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        .chat-container { max-width: 800px; margin: 20px auto; }
        .chat-box { max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px; padding: 15px; background-color: #f9f9f9; margin-bottom: 20px; }
        .message { margin-bottom: 15px; display: flex; align-items: flex-start; }
        .message.sent { justify-content: flex-end; }
        .message.received { justify-content: flex-start; }
        .message-content { max-width: 70%; padding: 10px; border-radius: 8px; position: relative; }
        .message.sent .message-content { background-color: #007bff; color: #fff; }
        .message.received .message-content { background-color: #e9ecef; color: #333; }
        .message img { max-width: 200px; border-radius: 8px; margin-top: 5px; }
        .message .sender { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .message .timestamp { font-size: 10px; color: #777; margin-top: 5px; }
        .chat-form { display: flex; gap: 10px; align-items: center; }
        .chat-form input[type="text"] { flex: 1; }
        .chat-form input[type="file"] { display: none; }
        .chat-form .file-label { cursor: pointer; color: #007bff; }
        .alert { border-radius: 8px; padding: 15px; margin-bottom: 20px; }
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
            <li><a href="family.php"><i class='bx bx-group'></i><span class="links_name">Family</span></a></li>
            <li><a href="chat.php" class="active"><i class='bx bx-message'></i><span class="links_name">Chat</span></a></li>
            <li><a href="user_profile.php"><i class='bx bx-cog'></i><span class="links_name">Setting</span></a></li>
            <li class="log_out"><a href="logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log Out</span></a></li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="title">Family Chat</span>
            </div>
            <div class="profile-details">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image">
                <span class="admin_name"><?php echo $name; ?></span>
                <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
                <ul class="profile-options" id='profile-options'>
                    <li><a href="user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="home-content">
            <div class="chat-container">
                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php } else { ?>
                    <div class="card">
                        <div class="card-header"><h4>Family Chat</h4></div>
                        <div class="card-body">
                            <div id="chat-box" class="chat-box"></div>
                            <form id="chat-form" enctype="multipart/form-data">
                                <div class="chat-form">
                                    <input type="text" id="chat-message" class="form-control" placeholder="Type a message...">
                                    <label class="file-label" for="chat-image"><i class="fas fa-image"></i></label>
                                    <input type="file" id="chat-image" name="image" accept="image/png,image/jpeg">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            function loadChat() {
                $.ajax({
                    url: 'get_chat.php',
                    method: 'GET',
                    data: { family_id: <?php echo $family_id ?: 0; ?> },
                    success: function(data) {
                        $('#chat-box').html(data);
                        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                    },
                    error: function(xhr, status, error) {
                        $('#chat-box').html('<p class="text-danger">Failed to load chat: ' + xhr.status + ' ' + xhr.statusText + ' (' + xhr.responseText + ')</p>');
                        console.error('Chat load error:', xhr.responseText, status, error);
                    }
                });
            }

            if (<?php echo $family_id ? 'true' : 'false'; ?>) {
                loadChat();
                setInterval(loadChat, 3000); // Poll every 3 seconds
            }

            $('#chat-form').submit(function(e) {
                e.preventDefault();
                var message = $('#chat-message').val().trim();
                var fileInput = $('#chat-image')[0].files[0];
                if (!message && !fileInput) return;

                var formData = new FormData();
                formData.append('message', message);
                if (fileInput) formData.append('image', fileInput);
                formData.append('family_id', <?php echo $family_id ?: 0; ?>);

                $.ajax({
                    url: 'send_chat.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response === "Message sent") {
                            $('#chat-message').val('');
                            $('#chat-image').val('');
                            loadChat();
                        } else {
                            $('#chat-box').append('<p class="text-danger">Failed to send message: ' + response + '</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#chat-box').append('<p class="text-danger">Error sending message: ' + xhr.status + ' ' + xhr.statusText + ' (' + xhr.responseText + ')</p>');
                        console.error('Chat send error:', xhr.responseText, status, error);
                    }
                });
            });

            let sidebar = document.querySelector(".sidebar");
            let sidebarBtn = document.querySelector(".sidebarBtn");
            sidebarBtn.onclick = function() {
                sidebar.classList.toggle("active");
                sidebarBtn.classList.toggle("bx-menu", !sidebar.classList.contains("active"));
                sidebarBtn.classList.toggle("bx-x", sidebar.classList.contains("active"));
            }

            const toggleButton = document.getElementById('profile-options-toggle');
            const profileOptions = document.getElementById('profile-options');
            toggleButton.addEventListener('click', () => {
                profileOptions.classList.toggle('show');
            });
        });
    </script>
</body>
</html>