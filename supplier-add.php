<?php
session_start();

// Rest of your code

if (!isset($_SESSION['user'])) {
    header('location: login.php');
}

$_SESSION['table'] = 'suppliers';
$_SESSION['redirect_to'] = 'supplier-add.php';
$user = $_SESSION['user'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Supplier - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>

<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php')?>
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-plus"></i>Create Supplier</h1>
                            <div id="userAddFormContainer">
                                <form action="database/add.php" method="POST" class="appForm " enctype="multipart/form-data">
                                    <div class="appFormInputContainer">
                                        <label for="supplier_name">Supplier Name</label>
                                        <input type="text" class="appFormInput" id="supplier_name" placeholder="Enter the supplier name..." name="supplier_name" />
                                    </div>

                                    <div class="appFormInputContainer">
                                        <label for="supplier_location">Location</label>
                                        <input type="text" class="appFormInput"  id ="supplier_location" placeholder="Enter the supplier location..." name="supplier_location" />

                                    </div>

                                    <div class="appFormInputContainer">
                                        <label for="email">Email</label>
                                        <input type="text" class="appFormInput"  id ="email" placeholder="Enter the supplier email.." name="email" />
                                    </div>

                                    <button type="submit" class="appBtn"><i class="fa fa-plus"></i> Create Supplier</button>
                                </form>
                                <?php
                                if (isset($_SESSION['response'])) {
                                    $response_message = $_SESSION['response']['message'];
                                    $is_success = $_SESSION['response']['success'];
                                    ?>
                                    <div class="responseMessage">
                                        <p class="responseMessage <?=$is_success ? 'responseMessage__success ' : 'responseMessage__error' ?>" >
                                            <?= $response_message ?>
                                        </p>
                                    </div>
                                <?php unset($_SESSION['response']);
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('partials/app-scripts.php'); ?>
</body>

</html>
