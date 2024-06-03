<?php
include('connection.php');
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id=:id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("
    SELECT supplier_name, suppliers.id
    FROM suppliers
    INNER JOIN productsuppliers ON productsuppliers.supplier = suppliers.id
    WHERE productsuppliers.product = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$row['suppliers'] = array_column($suppliers, 'supplier_name', 'id');

echo json_encode($row);
