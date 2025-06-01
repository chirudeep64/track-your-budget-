<?php
session_start();
error_reporting(0);
include('database.php');
if (strlen($_SESSION['detsuid']) == 0) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- jsPDF and html2canvas for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .card-body { background-color: #f8f9fa; border-radius: 8px; padding: 20px; }
        .table th, .table td { padding: 10px; text-align: center; vertical-align: middle; }
        .table th { background-color: #e9ecef; font-weight: bold; }
        .table tbody tr:nth-child(even) { background-color: #f2f2f2; }
        .btn-primary { background-color: #007bff; border: none; }
        .btn-primary:hover { background-color: #0056b3; }
        #printable { margin-top: 20px; }
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
            <li><a href="manage-expenses.php"><i class='bx bx-list-ul'></i><span class="links_name">Manage List</span></a></li>
            <li><a href="lending.php"><i class='bx bx-money'></i><span class="links_name">Lending</span></a></li>
            <li><a href="manage-lending.php"><i class='bx bx-coin-stack'></i><span class="links_name">Manage Lending</span></a></li>
            <li><a href="analytics.php"><i class='bx bx-pie-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
            <li><a href="report.php" class="active"><i class="bx bx-file"></i><span class="links_name">Report</span></a></li>
            <li><a href="user_profile.php"><i class='bx bx-cog'></i><span class="links_name">Setting</span></a></li>
            <li class="log_out"><a href="logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log out</span></a></li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="dashboard">Expenditure</span>
            </div>
            <div class="search-box">
                <input type="text" id="search-input" class="form-control form-control-sm mx-2" placeholder="Search...">
                <i class='bx bx-search'></i>
            </div>
            <?php
            $uid = $_SESSION['detsuid'];
            $ret = mysqli_query($db, "SELECT name FROM users WHERE id='$uid'");
            $row = mysqli_fetch_array($ret);
            $name = $row['name'];
            ?>
            <div class="profile-details">
                <img src="images/maex.png" alt="">
                <span class="admin_name"><?php echo $name; ?></span>
                <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
                <ul class="profile-options" id='profile-options'>
                    <li><a href="user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="home-content">
            <div class="overview-boxes">
                <div class="col-md-12">
                    <br>
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">Report</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="reportForm" method="GET" class="p-3">
                                <div class="form-group">
                                    <label for="reportType" class="font-weight-bold">Report Type</label>
                                    <select class="form-control" id="reportType" name="reportType" required>
                                        <option value="" selected disabled>Select a report type</option>
                                        <option value="expense">Expense Report</option>
                                        <option value="pending">Pending Report</option>
                                        <option value="received">Received Report</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="startDate" class="font-weight-bold">Start Date</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="endDate" class="font-weight-bold">End Date</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate" required>
                                    </div>
                                </div>
                                <button type="submit" name="generateReport" class="btn btn-primary btn-block">Generate Report</button>
                            </form>
                        </div>
                    </div>
                    <?php
                    if (isset($_GET['generateReport']) && $_GET['reportType'] === 'expense') {
                        $startDate = mysqli_real_escape_string($db, $_GET['startDate']);
                        $endDate = mysqli_real_escape_string($db, $_GET['endDate']);
                        $userid = $_SESSION['detsuid'];
                        $ret = mysqli_query($db, "SELECT ExpenseDate, category, Description, NoteDate, SUM(ExpenseCost) as totaldaily 
                            FROM `tblexpense` 
                            WHERE (ExpenseDate BETWEEN '$startDate' AND '$endDate') 
                            AND (UserId='$userid') 
                            GROUP BY ExpenseDate, category");
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">Expense Report</h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button class="btn btn-primary" onclick="generatePDF()">Download PDF</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" id="printable">
                                <h5 align="center" style="color:blue">Datewise Expense Report from <span style="color:red"><?php echo $startDate ?></span> to <span style="color:red"><?php echo $endDate ?></span></h5>
                                <hr />
                                <?php if (mysqli_num_rows($ret) > 0) { ?>
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>S.NO</th>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Registered Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        $totalsexp = 0;
                                        while ($row = mysqli_fetch_array($ret)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row['ExpenseDate']; ?></td>
                                            <td><?php echo $row['category']; ?></td>
                                            <td><?php echo $row['Description']; ?></td>
                                            <td><?php echo $row['NoteDate']; ?></td>
                                            <td><?php echo number_format($ttlsl = $row['totaldaily'], 2); ?></td>
                                        </tr>
                                        <?php
                                        $totalsexp += $ttlsl;
                                        $cnt++;
                                        } ?>
                                        <tr>
                                            <th colspan="5" style="text-align:center">Grand Total</th>
                                            <td><b><?php echo number_format($totalsexp, 2); ?></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php } else { ?>
                                    <p style='text-align:center'><b>No data found</b></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <script>
    $(document).ready(function() {
        var originalTableHtml = $('table tbody').html();
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            var found = false;
            if (value) {
                $('table tbody tr').filter(function() {
                    var matches = $(this).text().toLowerCase().indexOf(value) > -1;
                    $(this).toggle(matches);
                    if (matches) found = true;
                });
            } else {
                $('table tbody').html(originalTableHtml);
                found = true;
            }
            if (!found) {
                $('table tbody').html('<tr><td colspan="6" style="text-align:center;">No data found</td></tr>');
            }
        });
        $('#reportForm').on('submit', function(e) {
            var startDate = new Date($('#startDate').val());
            var endDate = new Date($('#endDate').val());
            if (startDate > endDate) {
                e.preventDefault();
                alert('Start Date cannot be later than End Date.');
            }
        });
    });

    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".sidebarBtn");
    sidebarBtn.onclick = function() {
        sidebar.classList.toggle("active");
        if (sidebar.classList.contains("active")) {
            sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else {
            sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    }
    const toggleButton = document.getElementById('profile-options-toggle');
    const profileOptions = document.getElementById('profile-options');
    toggleButton.addEventListener('click', () => {
        profileOptions.classList.toggle('show');
    });

    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        
        // Add header
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Expense Report', 105, 20, { align: 'center' });
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text(`Period: ${$('#startDate').val()} to ${$('#endDate').val()}`, 105, 28, { align: 'center' });
        doc.setLineWidth(0.5);
        doc.line(15, 32, 195, 32);

        // Capture the printable area
        html2canvas(document.querySelector('#printable'), {
            scale: 2,
            useCORS: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 180;
            const pageHeight = 295;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 35;

            doc.addImage(imgData, 'PNG', 15, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight + 35;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 15, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Add footer
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setFont('helvetica', 'italic');
                doc.text(`© ${new Date().getFullYear()} Expense Tracker | Page ${i} of ${pageCount}`, 105, 285, { align: 'center' });
            }

            doc.save(`Expense_Report_${new Date().toISOString().slice(0,10)}.pdf`);
        });
    }
    </script>
<?php } ?>