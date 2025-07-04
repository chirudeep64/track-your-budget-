<?php
session_start();
error_reporting(0);
include('database.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else{
?>
  
  <!DOCTYPE html>
<!-- Designined by CodingLab | www.youtube.com/codinglabyt -->
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <!--<title> Responsiive Admin Dashboard | CodingLab </title>-->
    <link rel="stylesheet" href="css/style.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">



     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
   </head>
<body>
  <div class="sidebar">
    <div class="logo-details">
      <i class='bx bx-album'></i>
      <span class="logo_name">Expenditure</span>
    </div>
      <ul class="nav-links">
        <li>
          <a href="home.php" >
            <i class='bx bx-grid-alt' ></i>
            <span class="links_name">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="add-expenses.php">
            <i class='bx bx-box' ></i>
            <span class="links_name">Expenses</span>
          </a>
        </li>
        <li>
          <a href="manage-expenses.php">
            <i class='bx bx-list-ul' ></i>
            <span class="links_name">Manage List</span>
          </a>
        </li>
        
        <li>
          <a href="lending.php">
          <i class='bx bx-money'></i>
            <span class="links_name">lending</span>
          </a>
        </li>
        <li>
        <a href="manage-lending.php" >
        <i class='bx bx-coin-stack'></i>
            <span class="links_name">Manage lending</span>
          </a>
        </li>
        <li>
          <a href="analytics.php">
            <i class='bx bx-pie-chart-alt-2' ></i>
            <span class="links_name">Analytics</span>
          </a>
        </li>
        <li>
          <a href="report.php" class="active">
          <i class="bx bx-file"></i>
            <span class="links_name">Report</span>
          </a>
        </li>
       <li>
       <a href="user_profile.php">
            <i class='bx bx-cog' ></i>
            <span class="links_name">Setting</span>
          </a>
        </li>
        <li class="log_out">
          <a href="logout.php">
            <i class='bx bx-log-out'></i>
            <span class="links_name">Log out</span>
          </a>
        </li>
      </ul>
  </div>
  <section class="home-section">
    <nav>
    <div class="sidebar-button">
        <i class='bx bx-menu sidebarBtn'></i>
        <span class="dashboard">Expenditure</span>
      </div>
   
      <div class="search-box">
        <input input type="text" id="search-input" class="form-control form-control-sm mx-2" placeholder="Search...">
        <i class='bx bx-search' ></i>
</div>
<script>
$(document).ready(function() {
    var originalTableHtml = $('table tbody').html(); // Store original table HTML
    
    // Handle keyup event of search input
    $('#search-input').on('keyup', function() {
        var value = $(this).val().toLowerCase(); // Get search keyword and convert to lowercase
        var found = false;
        
        if (value) { // If search input has value
            $('table tbody tr').filter(function() { // Filter table rows based on search keyword
                var matches = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(matches); // Show or hide table rows based on search keyword
                if(matches) found = true;
            });
        } else { // If search input is empty
            $('table tbody').html(originalTableHtml); // Show original table HTML
            found = true;
        }
        
        if(!found) {
            $('table tbody').html('<tr><td colspan="7" style="text-align:center;">No data found</td></tr>');
        }
    });
});

</script>

      <?php
$uid=$_SESSION['detsuid'];
$ret=mysqli_query($db,"select name  from users where id='$uid'");
$row=mysqli_fetch_array($ret);
$name=$row['name'];

?>

      <div class="profile-details">
  <img src="images/maex.png" alt="">
  <span class="admin_name"><?php echo $name; ?></span>
  <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
  <ul class="profile-options" id='profile-options'>
  <li><a href="user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
    <!-- <li><a href="#"><i class="fas fa-cog"></i> Account Settings</a></li> -->
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</div>


<script>
  const toggleButton = document.getElementById('profile-options-toggle');
  const profileOptions = document.getElementById('profile-options');
  
  toggleButton.addEventListener('click', () => {
    profileOptions.classList.toggle('show');
  });
</script>


    </nav>

<script>
function printReport() {
  // Remove the form submit event to prevent redirection
  $('#filter-form').off('submit');
  
  // Remove the URL from the report
  var url = document.URL;
  var printableContent = $('#printable').clone();
  var urlElement = printableContent.find('.url');
  if (urlElement.length) {
    urlElement.remove();
  }

  // Add table header and footer
  var tableHeader = '<table style="border-collapse: collapse; border-spacing: 0; width: 100%;">' +
                    '<thead>' +
                    '<tr style="border: 1px solid black;">' +
                    '<th style="border: 1px solid black;">S.NO</th>' +
                    '<th style="border: 1px solid black;">Date</th>' +
                    '<th style="border: 1px solid black;">Category</th>' +
                    '<th style="border: 1px solid black;">Description</th>' +
                    '<th style="border: 1px solid black;">Registered Date</th>' +
                    '<th style="border: 1px solid black;">Amount</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';
  
var tableFooter = '<tr>' +
                    '<td colspan="5" style="text-align:center; border: 1px solid black;">Grand Total &copy; 2023</td>' +
                    '<td style="border: 1px solid black;">6000</td>' +
                    '</tr>' +
                    '</tbody>' +
                    '</table>';

  
  printableContent.find('.expense-table').prepend(tableHeader).append(tableFooter);
  
  // Print the report
// Get the current date and format it as "YYYY-MM-DD"
var currentDate = new Date().toISOString().slice(0,10);

// Print the report with the current date in the title
var nw = window.open('', '_blank', 'width=900,height=600');
nw.document.write('<html><head><title>Expense Report - ' + currentDate + '</title></head><body>');
  nw.document.write('<style>table {border-collapse: collapse; border-spacing: 0;} td, th {border: 1px solid black; padding: 5px;}</style>');
  nw.document.write(printableContent.html());
  nw.document.write('</body></html>');
  nw.document.close();
  nw.focus();
  setTimeout(function() {
    nw.print();
    setTimeout(function() {
      nw.close();
      end_loader();
    }, 500);
  }, 500);
}

</script>


<?php
session_start();

$fdate = $_GET['startDate'];
$tdate = $_GET['endDate'];
$rtype = $_GET['reportType'];

?>
<div class="home-content">
  <div class="overview-boxes">
    <div class="col-md-12">
      <br>
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-6">
              <h4 class="card-title">Expense Report</h4>
            </div>
            <div class="col-md-6 text-right">
              <button class="btn btn-primary" onclick="printReport()">Print</button>
            </div>
          </div>
        </div>
        <div class="card-body" id="printable">
          <h5 align="center" style="color:blue">Datewise <?php echo ucfirst($rtype); ?> Report from <span style="color:red"><?php echo $fdate ?></span> to <span style="color:red"><?php echo $tdate ?></span></h5>
          <hr />
          <?php
          $userid=$_SESSION['detsuid'];
          $ret=mysqli_query($db,"SELECT ExpenseDate,category,Description,NoteDate,SUM(ExpenseCost) as totaldaily FROM `tblexpense`  where (ExpenseDate BETWEEN '$fdate' and '$tdate') && (UserId='$userid') group by ExpenseDate, category");
          if(mysqli_num_rows($ret) > 0) {
          ?>
          <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr>
                <th>S.NO</th>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Registered Date</th>
                <th><?php echo ucfirst($rtype); ?> Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $cnt=1;
              $totalsexp=0;
              while ($row=mysqli_fetch_array($ret)) {
              ?>
              <tr>
                <td><?php echo $cnt;?></td>
                <td><?php  echo $row['ExpenseDate'];?></td>
                <td><?php  echo $row['category'];?></td>
                <td><?php  echo $row['Description'];?></td>
                <td><?php  echo $row['NoteDate'];?></td>
                <td><?php  echo $ttlsl=$row['totaldaily'];?></td>
              </tr>
              <?php
              $totalsexp+=$ttlsl; 
              $cnt=$cnt+1;
              }?>
              <tr>
              <th colspan="5" style="text-align:center">Grand Total</th>
              <td><b><?php echo number_format($totalsexp, 2); ?></b></td>
            </tr>

            </tbody>
          </table>
          <?php
          } else {
            echo "<p style='text-align:center'><b>No data found</p>";
          }
          ?>
        </div>
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
if(sidebar.classList.contains("active")){
sidebarBtn.classList.replace("bx-menu" ,"bx-menu-alt-right");
}else
sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
}
</script>
<?php } ?>
<?php
require_once('tcpdf/tcpdf.php');
session_start();
include('database.php');

class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'Expense Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        // Add a line under the header
        $this->Line(15, 25, $this->getPageWidth() - 15, 25);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Expense Tracker');
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Expense Report');

// Set margins
$pdf->SetMargins(15, 35, 15);
$pdf->SetHeaderMargin(20);
$pdf->SetFooterMargin(15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

// Get date range from URL
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$userId = $_SESSION['detsuid'];

// Add report period
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' - ' . date('F d, Y', strtotime($endDate)), 0, 1, 'L');
$pdf->Ln(5);

// Add summary section
$query = mysqli_query($db, "SELECT 
    SUM(ExpenseCost) as total_expense,
    COUNT(*) as total_transactions,
    AVG(ExpenseCost) as avg_expense,
    MAX(ExpenseCost) as max_expense
    FROM tblexpense 
    WHERE (ExpenseDate BETWEEN '$startDate' AND '$endDate') 
    AND UserId='$userId'");
$summary = mysqli_fetch_assoc($query);

$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Summary', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 11);

$summaryData = array(
    array('Total Expenses:', 'Rs. ' . number_format($summary['total_expense'], 2)),
    array('Total Transactions:', $summary['total_transactions']),
    array('Average Expense:', 'Rs. ' . number_format($summary['avg_expense'], 2)),
    array('Highest Expense:', 'Rs. ' . number_format($summary['max_expense'], 2))
);

foreach($summaryData as $row) {
    $pdf->Cell(60, 8, $row[0], 1, 0, 'L', true);
    $pdf->Cell(130, 8, $row[1], 1, 1, 'L', true);
}

$pdf->Ln(10);

// Add expense details table
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Expense Details', 0, 1, 'L');

// Table header
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$header = array('Date', 'Category', 'Product', 'Amount', 'Notes');
$w = array(35, 40, 45, 30, 40);

foreach($header as $i => $col) {
    $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', true);
}
$pdf->Ln();

// Table data
$pdf->SetFont('helvetica', '', 10);
$pdf->SetFillColor(245, 245, 245);
$fill = false;

$query = mysqli_query($db, "SELECT * FROM tblexpense 
    WHERE (ExpenseDate BETWEEN '$startDate' AND '$endDate') 
    AND UserId='$userId' 
    ORDER BY ExpenseDate DESC");

while($row = mysqli_fetch_array($query)) {
    $pdf->Cell($w[0], 6, date('Y-m-d', strtotime($row['ExpenseDate'])), 'LR', 0, 'L', $fill);
    $pdf->Cell($w[1], 6, $row['category'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[2], 6, $row['product_name'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[3], 6, 'Rs. ' . number_format($row['ExpenseCost'], 2), 'LR', 0, 'R', $fill);
    $pdf->Cell($w[4], 6, substr($row['NoteDate'], 0, 20), 'LR', 0, 'L', $fill);
    $pdf->Ln();
    $fill = !$fill;
}

// Closing line
$pdf->Cell(array_sum($w), 0, '', 'T');

// Category breakdown
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Category Breakdown', 0, 1, 'L');

$query = mysqli_query($db, "SELECT 
    category, 
    COUNT(*) as count, 
    SUM(ExpenseCost) as total 
    FROM tblexpense 
    WHERE (ExpenseDate BETWEEN '$startDate' AND '$endDate') 
    AND UserId='$userId' 
    GROUP BY category");

$pdf->SetFont('helvetica', 'B', 11);
$header = array('Category', 'Count', 'Total', '% of Expenses');
$w = array(50, 30, 40, 40);

foreach($header as $i => $col) {
    $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('helvetica', '', 10);
while($row = mysqli_fetch_array($query)) {
    $percentage = ($row['total'] / $summary['total_expense']) * 100;
    $pdf->Cell($w[0], 6, $row['category'], 'LR', 0, 'L', $fill);
    $pdf->Cell($w[1], 6, $row['count'], 'LR', 0, 'C', $fill);
    $pdf->Cell($w[2], 6, 'Rs. ' . number_format($row['total'], 2), 'LR', 0, 'R', $fill);
    $pdf->Cell($w[3], 6, number_format($percentage, 1) . '%', 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Cell(array_sum($w), 0, '', 'T');

// Output the PDF
$pdf->Output('Expense_Report_' . date('Y-m-d') . '.pdf', 'I');
?>