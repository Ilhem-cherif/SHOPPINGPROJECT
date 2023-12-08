<?php
include 'components/connect.php';

if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'] . '%'; // Add '%' for partial matching
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE `name` LIKE :searchTerm OR code_categorie IN (SELECT code FROM `categorie` WHERE nom LIKE :searchTerm)");
    $select_products->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $select_products->execute();

    $results = $select_products->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>

