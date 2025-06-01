<?php
session_start();
error_reporting(0);
include('database.php');
if (strlen($_SESSION['detsuid']) == 0) {
    header('location:logout.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        canvas { width: 100%; height: auto; }
        .card, .card1 { border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px; padding: 20px; background-color: #fff; }
        .card { height: 500px; }
        .card-header { background-color: #f7f7f7; border-bottom: 1px solid #ddd; margin-bottom: 20px; padding: 10px; }
        .card-title { font-size: 24px; font-weight: bold; margin: 0; }
        .card-body { padding: 0; }
        .table { border-collapse: collapse; width: 100%; font-size: 16px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; padding: 10px 20px; border: 1px solid #ddd; }
        .table td { padding: 10px 20px; border-bottom: 1px solid #ddd; }
        .badge { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; padding: 5px 10px; }
        #add-button { position: fixed; bottom: 24px; right: 24px; border: none; border-radius: 50%; background-color: #4285f4; width: 64px; height: 64px; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); transition: all 0.2s ease-in-out; }
        #add-button:hover { transform: translateY(-2px); box-shadow: 0px 8px 16px rgba(0,0,0,0.2); background-color: #000; }
        #add-button i { font-size: 24px; color: #fff; transition: all 0.2s ease-in-out; }
        #add-button:hover i { transform: rotate(-45deg); }
        #add-button::before { content: "Add Expense"; position: absolute; top: 13px; left: -100%; transform: translateX(-50%); background-color: rgba(0,0,0,0.8); color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 14px; opacity: 0; transition: opacity 0.2s ease-in-out; }
        #add-button:hover::before { opacity: 1; left: -130%; }
        #chat-box { max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <i class='bx bx-album'></i>
            <span class="logo_name">Expenditure</span>
        </div>
        <ul class="nav-links">
            <li><a href="#" class="active"><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
            <li><a href="add-expenses.php"><i class='bx bx-box'></i><span class="links_name">Expenses</span></a></li>
            <li><a href="manage-expenses.php"><i class='bx bx-list-ul'></i><span class="links_name">Manage List</span></a></li>
            <li><a href="lending.php"><i class='bx bx-money'></i><span class="links_name">Lending</span></a></li>
            <li><a href="manage-lending.php"><i class='bx bx-coin-stack'></i><span class="links_name">Manage Lending</span></a></li>
            <li><a href="analytics.php"><i class='bx bx-pie-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
            <li><a href="report.php"><i class="bx bx-file"></i><span class="links_name">Report</span></a></li>
            <li><a href="family.php"><i class='bx bx-group'></i><span class="links_name">Family</span></a></li>
            <li><a href="user_profile.php"><i class='bx bx-cog'></i><span class="links_name">Setting</span></a></li>
            <li><a href="chat.php"><i class='bx bx-cog'></i><span class="links_name">chat with family</span></a></li>
            <li class="log_out"><a href="logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log out</span></a></li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="dashboard">Dashboard</span>
            </div>
            <div class="search-box">
                <input type="text" id="search-input" class="form-control form-control-sm mx-2" placeholder="Search...">
                <i class='bx bx-search'></i>
            </div>
            <?php
            $uid = $_SESSION['detsuid'];
            $ret = mysqli_query($db, "SELECT name, family_id FROM users WHERE id='$uid'");
            $row = mysqli_fetch_array($ret);
            $name = $row['name'];
            $family_id = $row['family_id'];
            ?>
            <div class="profile-details">
                <?php
                $profile_query = mysqli_query($db, "SELECT profile_image FROM users WHERE id='$uid'");
                if (!$profile_query) {
                    error_log("Database error: " . mysqli_error($db));
                    $profile_image = 'images/maex.png';
                } else {
                    $profile_data = mysqli_fetch_array($profile_query);
                    $profile_path = 'uploads/' . $profile_data['profile_image'];
                    $profile_image = ($profile_data['profile_image'] && file_exists($profile_path)) ? $profile_path : 'images/maex.png';
                }
                ?>
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image">
                <span class="admin_name"><?php echo htmlspecialchars($name); ?></span>
                <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
                <ul class="profile-options" id='profile-options'>
                    <li><a href="user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="home-content">
            <div class="overview-boxes">
                <?php
                $userid = $_SESSION['detsuid'];
                $tdate = date('Y-m-d');
                $ydate = date('Y-m-d', strtotime("-1 days"));
                $monthdate = date("Y-m-d", strtotime("-1 month"));
                $crrntdte = date("Y-m-d");

                $query = mysqli_query($db, "SELECT SUM(ExpenseCost) as todaysexpense FROM tblexpense WHERE ExpenseDate='$tdate' AND UserId='$userid'");
                $result = mysqli_fetch_array($query);
                $sum_today_expense = $result['todaysexpense'] ?? 0;

                $query1 = mysqli_query($db, "SELECT SUM(ExpenseCost) as yesterdayexpense FROM tblexpense WHERE ExpenseDate='$ydate' AND UserId='$userid'");
                $result1 = mysqli_fetch_array($query1);
                $sum_yesterday_expense = $result1['yesterdayexpense'] ?? 0;

                $query3 = mysqli_query($db, "SELECT SUM(ExpenseCost) as monthlyexpense FROM tblexpense WHERE ExpenseDate BETWEEN '$monthdate' AND '$crrntdte' AND UserId='$userid'");
                $result3 = mysqli_fetch_array($query3);
                $sum_monthly_expense = $result3['monthlyexpense'] ?? 0;

                $query5 = mysqli_query($db, "SELECT SUM(ExpenseCost) as totalexpense FROM tblexpense WHERE UserId='$userid'");
                $result5 = mysqli_fetch_array($query5);
                $sum_total_expense = $result5['totalexpense'] ?? 0;
                ?>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Today Expense</div>
                        <div class="number"><?php echo $sum_today_expense; ?></div>
                        <div class="indicator"><i class='bx bx-up-arrow-alt'></i><span class="text">Up from Today</span></div>
                    </div>
                    <i class='fas fa-circle-plus cart'></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Yesterday Expense</div>
                        <div class="number"><?php echo $sum_yesterday_expense; ?></div>
                        <div class="indicator"><i class='bx bx-up-arrow-alt'></i><span class="text">Up from yesterday</span></div>
                    </div>
                    <i class="fas fa-wallet cart two"></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Last 30 day Expense</div>
                        <div class="number"><?php echo $sum_monthly_expense; ?></div>
                        <div class="indicator"><i class='bx bx-up-arrow-alt'></i><span class="text">Up from Last 30 day</span></div>
                    </div>
                    <i class='fas fa-history cart three'></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Total Expense</div>
                        <div class="number"><?php echo $sum_total_expense; ?></div>
                        <div class="indicator"><i class='bx bx-up-arrow-alt up'></i><span class="text">Up from Year</span></div>
                    </div>
                    <i class='fas fa-piggy-bank cart four'></i>
                </div>
            </div>

            <!-- Family Transactions -->
            <div class="card1">
                <div class="card-header"><h5 class="card-title">Family Transactions</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="family-transactions">
                            <?php
                            if ($family_id) {
                                $query = "SELECT u.name, e.product_name, e.category, e.ExpenseCost, e.ExpenseDate 
                                        FROM tblexpense e 
                                        JOIN users u ON e.UserId = u.id 
                                        WHERE u.family_id = ? 
                                        ORDER BY e.ExpenseDate DESC LIMIT 10";
                                $stmt = mysqli_prepare($db, $query);
                                mysqli_stmt_bind_param($stmt, "i", $family_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                            <td>{$row['name']}</td>
                                            <td>" . ($row['product_name'] ?? 'N/A') . "</td>
                                            <td>{$row['category']}</td>
                                            <td>Rs. {$row['ExpenseCost']}</td>
                                            <td>" . date('Y-m-d', strtotime($row['ExpenseDate'])) . "</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Join a family to see transactions</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Spending Per Person -->
            <div class="card1">
                <div class="card-header"><h5 class="card-title">Spending Per Person</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Spent</th>
                                <th>Transaction Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($family_id) {
                                $query = "SELECT u.name, SUM(e.ExpenseCost) as total_spent, COUNT(e.id) as transaction_count 
                                        FROM users u 
                                        LEFT JOIN tblexpense e ON u.id = e.UserId 
                                        WHERE u.family_id = ? 
                                        GROUP BY u.id";
                                $stmt = mysqli_prepare($db, $query);
                                mysqli_stmt_bind_param($stmt, "i", $family_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                            <td>{$row['name']}</td>
                                            <td>Rs. " . ($row['total_spent'] ?? 0) . "</td>
                                            <td>" . ($row['transaction_count'] ?? 0) . "</td>
                                        </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Spending Per Product -->
            <div class="card1">
                <div class="card-header"><h5 class="card-title">Spending Per Product</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total Spent</th>
                                <th>Transaction Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($family_id) {
                                $query = "SELECT product_name, SUM(ExpenseCost) as total_spent, COUNT(id) as transaction_count 
                                        FROM tblexpense e 
                                        JOIN users u ON e.UserId = u.id 
                                        WHERE u.family_id = ? 
                                        GROUP BY product_name";
                                $stmt = mysqli_prepare($db, $query);
                                mysqli_stmt_bind_param($stmt, "i", $family_id);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                            <td>" . ($row['product_name'] ?? 'N/A') . "</td>
                                            <td>Rs. " . ($row['total_spent'] ?? 0) . "</td>
                                            <td>" . ($row['transaction_count'] ?? 0) . "</td>
                                        </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chat System -->
            <div class="card1">
                <div class="card-header"><h5 class="card-title">Family Chat</h5></div>
                <div class="card-body">
                    <div id="chat-box"></div>
                    <form id="chat-form">
                        <div class="form-group">
                            <input type="text" id="chat-message" class="form-control" placeholder="Type a message..." required>
                        </div>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>

            <!-- Expense Chart -->
            <div class="card">
                <div class="card-header"><h5 class="card-title">Expense Chart</h5></div>
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>

            <!-- Category Table -->
            <div class="card1">
                <div class="card-header"><h5 class="card-title">Category Table</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Percentage</th>
                                <th>Category</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="expense-table-body"></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Total</th>
                                <th>Rs. <span id="total-expense"></span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <button id="add-button" title="Add Expense"><i class="fas fa-plus"></i></button>

    <script>
        $(document).ready(function() {
            var originalTableHtml = $('#family-transactions').html();
            $('#search-input').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                var found = false;
                if (value) {
                    $('#family-transactions tr').filter(function() {
                        var matches = $(this).text().toLowerCase().indexOf(value) > -1;
                        $(this).toggle(matches);
                        if (matches) found = true;
                    });
                } else {
                    $('#family-transactions').html(originalTableHtml);
                    found = true;
                }
                if (!found) {
                    $('#family-transactions').html('<tr><td colspan="5" style="text-align:center;">No data found</td></tr>');
                }
            });

            // Chat functionality
            function loadChat() {
                $.ajax({
                    url: 'get_chat.php',
                    method: 'GET',
                    success: function(data) {
                        $('#chat-box').html(data);
                        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                    },
                    error: function(xhr, status, error) {
                        $('#chat-box').html('<p class="text-danger">Failed to load chat. Please try again.</p>');
                        console.error('Chat load error:', xhr.responseText, status, error);
                    }
                });
            }
            loadChat();
            setInterval(loadChat, 3000);

            $('#chat-form').submit(function(e) {
                e.preventDefault();
                var message = $('#chat-message').val().trim();
                if (message) {
                    $.ajax({
                        url: 'send_chat.php',
                        method: 'POST',
                        data: { message: message },
                        success: function(response) {
                            if (response === "Message sent") {
                                $('#chat-message').val('');
                                loadChat();
                            } else {
                                $('#chat-box').append('<p class="text-danger">Failed to send message: ' + response + '</p>');
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#chat-box').append('<p class="text-danger">Error sending message. Please try again.</p>');
                            console.error('Chat send error:', xhr.responseText, status, error);
                        }
                    });
                }
            });

            // Chart code
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php
                        $query = mysqli_query($db, "SELECT ExpenseDate, SUM(ExpenseCost) as total_cost FROM tblexpense WHERE UserId='$userid' AND ExpenseDate > DATE_SUB(NOW(), INTERVAL 30 day) GROUP BY ExpenseDate");
                        $data = [];
                        $labels = [];
                        while ($result = mysqli_fetch_array($query)) {
                            $data[] = (float)$result['total_cost'];
                            $labels[] = date('Y-m-d', strtotime($result['ExpenseDate']));
                        }
                        echo json_encode($labels);
                    ?>,
                    datasets: [{
                        label: 'Expenses',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(224, 82, 96, 0.5)',
                        borderColor: '#e05260',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{ type: 'time', time: { unit: 'day', tooltipFormat: 'll' }, ticks: { source: 'auto' } }],
                        yAxes: [{ ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: 'Expense Cost' } }]
                    },
                    animation: { duration: 1000, easing: 'easeInOutQuad' },
                    legend: { display: false },
                    tooltips: { enabled: false },
                    hover: { mode: 'nearest', intersect: true },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Category table code
            fetch('pie-data.php')
                .then(response => response.json())
                .then(data => {
                    const total = data.reduce((acc, curr) => acc + curr.total_expense, 0);
                    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#8E44AD', '#3498DB', '#FFA07A', '#6B8E23', '#FF00FF', '#FFD700', '#00FFFF'];
                    const rows = data.map((item, i) => {
                        const percentage = ((item.total_expense / total) * 100).toFixed(2);
                        const color = colors[i % colors.length];
                        return `
                            <tr>
                                <td><span class="badge badge-pill badge-primary" style="background-color: ${color}">${percentage}%</span></td>
                                <td>${item.category}</td>
                                <td>Rs. ${item.total_expense.toFixed(2)}</td>
                            </tr>
                        `;
                    }).join('');
                    document.getElementById('expense-table-body').innerHTML = rows;
                    document.getElementById('total-expense').innerHTML = total.toFixed(2);
                });

            const toggleButton = document.getElementById('profile-options-toggle');
            const profileOptions = document.getElementById('profile-options');
            toggleButton.addEventListener('click', () => {
                profileOptions.classList.toggle('show');
            });

            const addButton = document.getElementById('add-button');
            addButton.addEventListener('click', () => {
                addButton.style.transform = 'translateX(50px)';
                setTimeout(() => { window.location.href = "add-expenses.php"; }, 200);
            });

            let sidebar = document.querySelector(".sidebar");
            let sidebarBtn = document.querySelector(".sidebarBtn");
            sidebarBtn.onclick = function() {
                sidebar.classList.toggle("active");
                sidebarBtn.classList.toggle("bx-menu", !sidebar.classList.contains("active"));
                sidebarBtn.classList.toggle("bx-menu-alt-right", sidebar.classList.contains("active"));
            }
        });
    </script>
</body>
</html>