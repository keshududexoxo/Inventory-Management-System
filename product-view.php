<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit(); // Ensure script stops execution after redirect
}

$show_table = 'products';
$products = include('database/show.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Products - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>

<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php'); ?>
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-list"></i>List of Products</h1>
                            <div class="section_content">
                                <div class="users">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>Description</th>
                                                <th>Suppliers</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $index => $product) { ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td class="firstName">
                                                        <img class="productImages" src="uploads/products/<?= $product['img'] ?>" alt="" />
                                                    </td>
                                                    <td class="lastName"><?= $product['product_name'] ?></td>
                                                    <td class="email"><?= $product['description'] ?></td>
                                                    <td class="email">
                                                        <?php
                                                        $supplier_list = '-';
                                                        $pid = $product['id'];
                                                        $stmt = $conn->prepare("SELECT supplier_name 
                                                            FROM suppliers, productsuppliers
                                                            WHERE productsuppliers.product = :pid
                                                            AND productsuppliers.supplier = suppliers.id");
                                                        $stmt->bindParam(':pid', $pid); // Binding parameters to prevent SQL injection
                                                        $stmt->execute();
                                                        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        if ($row) {
                                                            $supplier_arr = array_column($row, 'supplier_name');
                                                            $supplier_list = '<li>' . implode("</li><li>", $supplier_arr);
                                                        }

                                                        // Closing the <li> tag
                                                        echo $supplier_list;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        try {
                                                            $pid = $product['created_by'];
                                                            $stmt = $conn->prepare("SELECT * FROM users WHERE id=:pid");
                                                            $stmt->bindParam(':pid', $pid); // Bind $pid to the :pid placeholder
                                                            $stmt->execute();
                                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                                            $created_by_name = $row['first_name'] . ' ' . $row['last_name'];
                                                            echo $created_by_name;
                                                        } catch (PDOException $e) {
                                                            echo "Error: " . $e->getMessage();
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= date('M d,Y @ h:i:s A', strtotime($product['created_at'])) ?></td>
                                                    <td><?= date('M d,Y @ h:i:s A', strtotime($product['updated_at'])) ?></td>
                                                    <td>
                                                        <a href="#" class="updateProduct" data-pid="<?= $product['id'] ?>"><i class="fa fa-pencil"></i>Edit</a>
                                                        <a href="#" class="deleteProduct" data-name="<?= $product['product_name'] ?>" data-pid="<?= $product['id'] ?>"><i class="fa fa-trash"></i>Delete</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <!-- Display the count of products -->
                                    <p class="userCount"><?= count($products) ?> products</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('partials/app-scripts.php');
    $show_table = 'suppliers';
    $suppliers = include('database/show.php');

    $suppliers_arr = [];

    foreach ($suppliers as $supplier) {
        $suppliers_arr[$supplier['id']] = $supplier['supplier_name'];
    }

    $supplier_arr = json_encode($suppliers_arr);
    ?>
    Delete and update

    <script>
        function registerEvents() {
            document.addEventListener('click', function(e) {
                var targetElement = e.target;
                var classList = targetElement.classList;

                if (classList.contains('deleteProduct')) {
                    e.preventDefault();
                    var pId = targetElement.dataset.pid;
                    var pName = targetElement.dataset.name;

                    BootstrapDialog.confirm({
                        type: BootstrapDialog.TYPE_DANGER,
                        title: 'Delete Product',
                        message: 'Are you sure you want to delete <strong>' + pName + '</strong>?',
                        callback: function(isDelete) {
                            if (isDelete) {
                                $.ajax({
                                    method: 'POST',
                                    data: {
                                        id: pId,
                                        table: 'products'
                                    },
                                    url: 'database/delete.php',
                                    dataType: 'json',
                                    success: function(data) {
                                        console.log(data); // Log response for debugging
                                        var message = data.success ? (pName + ' Successfully deleted!') : 'Error processing your request!';

                                        BootstrapDialog.alert({
                                            type: data.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER,
                                            message: message,
                                            callback: function() {
                                                if (data.success) location.reload();
                                            }
                                        });
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(xhr.responseText); // Log error response for debugging
                                        BootstrapDialog.alert({
                                            type: BootstrapDialog.TYPE_DANGER,
                                            message: 'Error: ' + error
                                        });
                                    }
                                });
                            }
                        }

                    });

                }

                if (classList.contains('updateProduct')) {
                    e.preventDefault();
                    var pId = targetElement.dataset.pid;

                    showEditDialog(pId);
                }
            });

            document.addEventListener('submit', function(e) {
                e.preventDefault();
                var targetElement = e.target;

                if (targetElement.id === 'editProductForm') {
                    saveUpdatedData(targetElement);
                }
            });
        }

        function saveUpdatedData(form) {
            $.ajax({
                method: 'POST',
                data: new FormData(form),
                url: 'database/update-product.php',
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    BootstrapDialog.alert({
                        type: data.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER,
                        message: data.message,
                        callback: function() {
                            if (data.success)
                                location.reload();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    BootstrapDialog.alert({
                        type: BootstrapDialog.TYPE_DANGER,
                        message: 'Error: ' + error
                    });
                }
            });
        }

        function showEditDialog(id) {
            $.get('database/get-product.php', {
                id: id
            }, function(productDetails) {
                BootstrapDialog.confirm({
                    title: 'Update Product: ' + productDetails.product_name,
                    message: `<form action="database/update-product.php" method="POST" enctype="multipart/form-data" id="editProductForm">
           <div class="appFormInputContainer">
               <label for="product_name">Product Name</label>
               <input type="text" class="appFormInput" id="product_name" value="${productDetails.product_name}" placeholder="Enter the product name..." name="product_name" />
           </div>
           <div class="appFormInputContainer">
               <label for="description">Suppliers</label>
               <select name="suppliers[]" id="suppliersSelect" multiple="">
                   <option value="">Select Supplier</option>
                   <?php
                   $show_table="suppliers";
                   $suppliers = include("database/show.php");
                   foreach($suppliers as $supplier) {
                       echo "<option value='" . $supplier["id"] . "'>" . $supplier["supplier_name"] . "</option>";
                   }
                   ?>
               </select>
           </div>
           <div class="appFormInputContainer">
               <label for="description">Description</label>
               <textarea class="appFormInput productTextAreaInput" placeholder="Enter the product description" id="description"  name="description">${productDetails.description}</textarea>
           </div>
           <div class="appFormInputContainer">
               <label for="product_image">Product Image</label>
               <input type="file" name="img" />
           </div>
           <input type="hidden" name="pid" value="${productDetails.id}"/>
           <input type="submit" value="Submit" id="editProductSubmitBtn" class="hidden"/>
           </form>`,
                    callback: function(isUpdate) {
                        if (isUpdate) {
                            document.getElementById('editProductSubmitBtn').click();
                        }
                    }
                });
            }, 'json');
        }

        function initialize() {
            registerEvents();
        }

        initialize();
    </script>

</body>

</html>
