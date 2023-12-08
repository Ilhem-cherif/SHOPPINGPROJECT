<?php
include '../components/connect.php';

if(isset($_POST['search_term'])){
   $search_term = '%' . $_POST['search_term'] . '%';

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? OR code_categorie IN (SELECT code FROM `categorie` WHERE nom LIKE ?)");
   $select_products->execute([$search_term, $search_term]);

   $products = array();

   while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
      $products[] = $fetch_products;
   }

   echo json_encode($products);
}
?>
