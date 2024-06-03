<?php

session_start();

include('table_columns.php'); // Include the database connection file

$table_name = $_SESSION['table'];
$columns = $table_columns_mapping[$table_name];

$db_arr = [];
$user = $_SESSION['user'];

try {
    foreach ($columns as $column) {
        // handling different column cases
        if (in_array($column, ['created_at', 'updated_at'])) {
            $value = date('Y-m-d H:i:s');
        } elseif ($column == 'created_by') {
            $value = $user['id'];
        } elseif ($column == 'password') {
            $value = password_hash($_POST[$column], PASSWORD_DEFAULT);
        } elseif ($column == 'img') {
            // handling file upload for 'img' column
            $target_dir = "../uploads/products/";
            $file_data = $_FILES[$column];
            
            // Check if file is uploaded successfully
            if ($file_data['error'] !== UPLOAD_ERR_OK) {
                $response = [
                    'success' => false,
                    'message' => 'Error uploading file: ' . $file_data['error']
                ];
                $_SESSION['response'] = $response;
                header('location:../' . $_SESSION['redirect_to']);
                exit; // Stop further execution
            }
            $value = NULL;
            $file_data = $_FILES['img'];

            if ($file_data['tmp_name'] !== '') {
                $file_name = $file_data['name'];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $file_name = 'product-' . time() . '.' . $file_ext;

                $check = getimagesize($file_data['tmp_name']);

                if ($check) {
                    if (move_uploaded_file($file_data['tmp_name'], $target_dir . $file_name)) {
                        $value = $file_name;
                    }
                }
            } else {
                $value = isset($_POST[$column]) ? $_POST[$column] : '';
            }
        } elseif ($column == 'first_name') {
            // Ensure that first_name is not null
            $value = isset($_POST[$column]) ? $_POST[$column] : '';
            if (empty($value)) {
                throw new Exception("First name cannot be null.");
            }
        } else {
            // For other columns, get the value from $_POST
            $value = isset($_POST[$column]) ? $_POST[$column] : '';
        }
        $db_arr[$column] = $value;
    }

    $table_properties = implode(", ", array_keys($db_arr));
    $table_placeholders = ':' . implode(", :", array_keys($db_arr));

    $sql = "INSERT INTO
            $table_name($table_properties)
            VALUES
            ($table_placeholders)";

    include('connection.php');
    $stmt = $conn->prepare($sql);
    $stmt->execute($db_arr);

    $product_id = $conn->lastInsertId();

    if ($table_name === 'products') {
        $suppliers = isset($_POST['suppliers']) ? $_POST['suppliers'] : [];
        if ($suppliers) {
            foreach ($suppliers as $supplier) {
                $supplier_data = [
                    'supplier_id' => $supplier,
                    'product_id' => $product_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $sql = "INSERT INTO productsuppliers
                (supplier,product,updated_at,created_at)
                VALUES
                (:supplier_id,:product_id,:updated_at,:created_at)";
        
                $stmt = $conn->prepare($sql);
                $stmt->execute($supplier_data);
            }
        }
    }

    $response = [
        'success' => true,
        'message' => 'Successfully added to the system.'
    ];
} catch (PDOException $e) {
    // Handle the exception
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
} catch (Exception $e) {
    // Handle the exception
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

$_SESSION['response'] = $response;
header('location:../' . $_SESSION['redirect_to']);
