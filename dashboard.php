<?php
session_start();
// Rest of your code

if(!isset($_SESSION['user'])) header('location: login.php');

$user =$_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>DashBoard - Inventory Management System</title>

    <link rel="stylesheet" type="text/css" href="./css/login.css/login.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
</head>
<body>
    <div id="dashboardMainContainer">
          <?php include('partials/app-sidebar.php')?>
        <div class="dashboard_content_container" id="dashboard_content_container">
        <?php include('partials/app-topnav.php')?>
            
           <div class="dashboard_content">
               <div class="dashboard_content_main">

               </div>
            </div>
         </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html> 