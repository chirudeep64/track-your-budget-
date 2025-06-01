<?php
session_start();
error_reporting(0);
include('database.php');

if (strlen($_SESSION['detsuid']) == 0) {
    header('location:logout.php');
    exit();
}

if (isset($_POST['submit'])) {
    $userid = $_SESSION['detsuid'];
    $dateexpense = $_POST['dateexpense'];
    $categoryId = $_POST['category'];
    $description = $_POST['category-description'];
    $costitem = $_POST['costitem'];
    $product_name = $_POST['product_name'];

    // Use prepared statement to insert expense
    $query = "INSERT INTO tblexpense (UserId, ExpenseDate, CategoryId, category, ExpenseCost, Description, product_name) 
              SELECT ?, ?, ?, CategoryName, ?, ?, ? FROM tblcategory WHERE CategoryId = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "isissis", $userid, $dateexpense, $categoryId, $costitem, $description, $product_name, $categoryId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script type='text/javascript'>alert('Expense added successfully'); window.location.href = 'manage-expenses.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Expense could not be added');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <style>
        .container {
            background-color: #f2f2f2;
            border-radius: 5px;
            box-shadow: 0px 0px 10px #aaa;
            padding: 20px;
            margin-top: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 3px;
            border: 1px solid #ccc;
        }
        .invalid-feedback {
            color: red;
            font-size: 12px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
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
            <li><a href="#" class="active"><i class='bx bx-box'></i><span class="links_name">Expenses</span></a></li>
            <li><a href="manage-expenses.php"><i class='bx bx-list-ul'></i><span class="links_name">Manage List</span></a></li>
            <li><a href="lending.php"><i class='bx bx-money'></i><span class="links_name">Lending</span></a></li>
            <li><a href="manage-lending.php"><i class='bx bx-coin-stack'></i><span class="links_name">Manage Lending</span></a></li>
            <li><a href="analytics.php"><i class='bx bx-pie-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
            <li><a href="report.php"><i class="bx bx-file"></i><span class="links_name">Report</span></a></li>
            <li><a href="family.php"><i class='bx bx-group'></i><span class="links_name">Family</span></a></li>
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
            <div class="col-md-12">
                <br>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Add Expense</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#add-category-modal">
                                    <i class="fas fa-plus-circle"></i> Add Category
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="expense-form" role="form" method="post" action="" class="needs-validation">
                            <div class="form-group">
                                <label for="dateexpense">Date of Expense</label>
                                <input class="form-control" type="date" id="dateexpense" name="dateexpense" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="" selected disabled>Choose Category</option>
                                    <?php
                                    $userid = $_SESSION['detsuid'];
                                    $query = "SELECT * FROM tblcategory WHERE UserId = ?";
                                    $stmt = mysqli_prepare($db, $query);
                                    mysqli_stmt_bind_param($stmt, "i", $userid);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['CategoryId'] . '">' . $row['CategoryName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input class="form-control" type="text" id="product_name" name="product_name" required>
                            </div>
                            <div class="form-group">
                                <label for="costitem">Cost of Item</label>
                                <input class="form-control" type="number" id="costitem" name="costitem" required>
                            </div>
                            <div class="form-group">
                                <label for="category-description">Description</label>
                                <textarea class="form-control" id="category-description" name="category-description" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="submit">Add</button>
                            </div>
                        </form>
                        <div id="success-message" class="alert alert-success" style="display:none;">
                            Expense added successfully.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="add-category-modal" tabindex="-1" role="dialog" aria-labelledby="add-category-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="add-category-form" method="post" action="add_category.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="add-category-modal-title">Add Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="category-name">Category Name</label>
                                <input type="text" class="form-control" id="category-name" name="category-name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="add-category-submit">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            // Form validation for expense form
            $("#expense-form").validate({
                rules: {
                    dateexpense: { required: true },
                    category: { required: true },
                    product_name: { required: true },
                    costitem: { required: true, number: true, min: 0 },
                    "category-description": { required: true }
                },
                messages: {
                    dateexpense: "Please select a date",
                    category: "Please select a category",
                    product_name: "Please enter a product name",
                    costitem: {
                        required: "Please enter the cost",
                        number: "Please enter a valid number",
                        min: "Cost cannot be negative"
                    },
                    "category-description": "Please enter a description"
                },
                errorElement: "div",
                errorClass: "invalid-feedback",
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });

            // Add category form validation and submission
            $("#add-category-form").validate({
                rules: {
                    "category-name": { required: true }
                },
                messages: {
                    "category-name": "Please enter a category name"
                },
                errorElement: "div",
                errorClass: "invalid-feedback",
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: 'add_category.php',
                        method: 'POST',
                        data: $(form).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#add-category-modal').modal('hide');
                                alert(response.message);
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Failed to add category. Please try again.');
                            console.error('AJAX error:', xhr.responseText, status, error);
                        }
                    });
                }
            });

            // Profile and sidebar toggle
            const toggleButton = document.getElementById('profile-options-toggle');
            const profileOptions = document.getElementById('profile-options');
            toggleButton.addEventListener('click', () => {
                profileOptions.classList.toggle('show');
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
            };

            // Debug modal trigger
            $('[data-toggle="modal"]').on('click', function() {
                console.log('Modal trigger clicked');
                $('#add-category-modal').modal('show');
            });
        });
    </script>
</body>
</html>