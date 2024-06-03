<?php
session_start();

// Rest of your code

if (!isset($_SESSION['user'])) {
    header('location: login.php');
}

$_SESSION['table'] = 'users';
$_SESSION['redirect_to'] = 'users-add.php';

$show_table='users';
$users=include('database/show.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add User - Inventory Management System</title>
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
                            <h1 class="section_header"><i class="fa fa-plus"></i>Create Product</h1>
                            <div id="userAddFormContainer">
                                <form action="database/add.php" method="POST" class="appForm ">
                                    <div class="appFormInputContainer">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" id="email" name="email" />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="password">Password</label>
                                        <input type="password" class="appFormInput" id="password" name="password" />
                                    </div>
                                    <button type="submit" class="appBtn"><i class="fa fa-plus"></i> Add User</button>
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
