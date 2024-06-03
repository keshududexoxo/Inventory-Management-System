<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit(); // Ensure script stops execution after redirect
}

$show_table = 'suppliers';
$suppliers = include('database/show.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Suppliers - Inventory Management System</title>
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
                            <h1 class="section_header"><i class="fa fa-list"></i>List of Suppliers</h1>
                            <div class="section_content">
                                <div class="users">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Supplier Name</th>
                                                <th>Supplier Location</th>
                                                <th>Contact Details</th>
                                                <th>Products</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($suppliers as $index => $supplier) { ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= $supplier['supplier_name'] ?></td>
                                                    <td><?= $supplier['supplier_location'] ?></td>
                                                    <td><?= $supplier['email'] ?></td>
                                                    <td>
                                                        <?php
                                                        $supplier_list = '-';
                                                        $sid = $supplier['id'];
                                                        $stmt = $conn->prepare("SELECT products.product_name 
                                                            FROM products INNER JOIN productsuppliers
                                                            ON products.id = productsuppliers.product
                                                            WHERE productsuppliers.supplier = :sid");
                                                        $stmt->bindParam(':sid', $sid);
                                                        $stmt->execute();
                                                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        if ($products) {
                                                            foreach ($products as $product) {
                                                                echo $product['product_name'] . '<br>';
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        try {
                                                            $created_by_id = $supplier['created_by'];
                                                            $stmt = $conn->prepare("SELECT * FROM users WHERE id=:created_by_id");
                                                            $stmt->bindParam(':created_by_id', $created_by_id);
                                                            $stmt->execute();
                                                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                                            $created_by_name = $user['first_name'] . ' ' . $user['last_name'];
                                                            echo $created_by_name;
                                                        } catch (PDOException $e) {
                                                            echo "Error: " . $e->getMessage();
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= date('M d,Y @ h:i:s A', strtotime($supplier['created_at'])) ?></td>
                                                    <td><?= date('M d,Y @ h:i:s A', strtotime($supplier['updated_at'])) ?></td>
                                                    <td>
                                                        <a href="#" class="updateProduct" data-pid="<?= $supplier['id'] ?>"><i class="fa fa-pencil"></i>Edit</a>
                                                        <a href="#" class="deleteProduct" data-name="<?= $supplier['supplier_name'] ?>" data-pid="<?= $supplier['id'] ?>"><i class="fa fa-trash"></i>Delete</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <!-- Display the count of suppliers -->
                                    <p class="userCount"><?= count($suppliers) ?> suppliers</p>
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
                        title: 'Delete Supplier',
                        message: 'Are you sure you want to delete <strong>' + supplierName + '</strong>?',
                        callback: function(isDelete) {
                            if (isDelete) {
                                $.ajax({
                                    method: 'POST',
                                    data: {
                                        id: pId,
                                        table: 'suppliers'
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
                url: 'database/update-supplier.php',
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
            $.get('database/get-supplier.php', {
                id: id
            }, function(supplierDetails) {
                BootstrapDialog.confirm({
                    title: 'Update Supplier: ' + supplierDetails.supplier_name,
                    message: `<form action="database/update-supplier.php" method="POST" id="editSupplierForm">
           <div class="appFormInputContainer">
               <label for="supplier_name">Supplier Name</label>
               <input type="text" class="appFormInput" id="supplier_name" value="${supplierDetails.supplier_name}" placeholder="Enter the supplier name..." name="supplier_name" />
           </div>
           <div class="appFormInputContainer">
               <label for="supplier_location">Supplier Location</label>
               <input type="text" class="appFormInput" id="supplier_location" value="${supplierDetails.supplier_location}" placeholder="Enter the supplier location..." name="supplier_location" />
           </div>
           <div class="appFormInputContainer">
               <label for="contact_details">Contact Details</label>
               <input type="text" class="appFormInput" id="contact_details" value="${supplierDetails.contact_details}" placeholder="Enter the contact details..." name="contact_details" />
           </div>
           <input type="hidden" name="sid" value="${supplierDetails.id}"/>
           <input type="submit" value="Submit" id="editSupplierSubmitBtn" class="hidden"/>
           </form>`,
                    callback: function(isUpdate) {
                        if (isUpdate) {
                            document.getElementById('editSupplierSubmitBtn').click();
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
