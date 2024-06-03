<?php
session_start();

// Rest of your code

if (!isset($_SESSION['user'])) {
    header('location: login.php');
}
$show_table='products';
$products=include('database/show.php');
$products=json_encode($products);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Product - Inventory Management System</title>
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
                            <h1 class="section_header"><i class="fa fa-plus"></i>Order Product</h1>
                            <div>
                                <button>Add New Product Order</button>
                            </div>
                            <div>
                                    <div>
                                        <label for="product_name">Product Name</label>
                                        <select name="product_name" id="product_name">
                                            <option value="">Product 1</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="product_name">Supplier Name</label>
                                        <input type="text" class="appFormInput" id="product_name" placeholder="Enter the product name..." name="product_name" />
                                    </div>

                                    <div>
                                        <label for="product_name">Quantity</label>
                                        <input type="text" class="appFormInput" id="product_name" placeholder="Enter the product name..." name="product_name" />
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('partials/app-scripts.php'); ?>
    <script>
        var products=<?=$products ?>;
        console.log(products);
    </script>
</body>

</html>
