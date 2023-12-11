<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['add_category'])){

   $name = $_POST['nom'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $select_categories = $conn->prepare("SELECT * FROM `categorie` WHERE nom = ?");
   $select_categories->execute([$name]);

   if($select_categories->rowCount() > 0){
      $message[] = 'category name already exists!';
   } else {

      $insert_categories = $conn->prepare("INSERT INTO `categorie`(nom) VALUES(?)");
      $insert_categories->execute([$name]);

      if(!$insert_categories){
        $message[] = 'new category added!';
      }

   }
}

if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  // Select products related to the deleted category
  $select_products = $conn->prepare("SELECT * FROM `products` WHERE code_categorie = ?");
  $select_products->execute([$delete_id]);

  // Delete each related product
  while ($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
     unlink('../uploaded_img/' . $product['image_01']);
     unlink('../uploaded_img/' . $product['image_02']);
     $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
     $delete_product->execute([$product['id']]);
     $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
     $delete_cart->execute([$product['id']]);
     $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
     $delete_wishlist->execute([$product['id']]);
  }

  // Now delete the category
  $delete_category = $conn->prepare("DELETE FROM `categorie` WHERE code = ?");
  $delete_category->execute([$delete_id]);

  header('location:categories.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Categories</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">

   <h1 class="heading">Add Category</h1>

   <form action="" method="post" >
      <div class="flex">
         <div class="inputBox">
            <span>Category Name (required)</span>
            <input type="text" class="box" required maxlength="100" placeholder="Enter category name" name="nom">
         </div>
      </div>
      <input type="submit" value="Add Category" class="btn" name="add_category">
   </form>

</section>

<section class="show-products">

   <h1 class="heading">Categories Added</h1>

   <div class="box-container">

   <?php
      $select_categories = $conn->prepare("SELECT * FROM `categorie`");
      $select_categories->execute();
      if($select_categories->rowCount() > 0){
         while($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <div class="name"><?= $fetch_categories['nom']; ?></div>
      <div class="flex-btn">
         <a href="update_category.php?update=<?= $fetch_categories['code']; ?>" class="option-btn">Update</a>
         <a href="categories.php?delete=<?= $fetch_categories['code']; ?>" class="delete-btn" onclick="return confirm('Delete this category?');">Delete</a>
      </div>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No categories added yet!</p>';
      }
   ?>
   
   </div>

</section>

<script src="../js/admin_script.js"></script>
   
</body>
</html>
