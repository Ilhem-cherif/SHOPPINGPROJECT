<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update'])){
   $pid = $_POST['pid'];
   $name = $_POST['nom'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $update_category = $conn->prepare("UPDATE `categorie` SET nom = ? WHERE code = ?");
   $update_category->execute([$name,$pid]);

   $message[] = 'category updated successfully!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update product</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="update-product">

   <h1 class="heading">update category</h1>

   <?php
    $update_id = $_GET['update'];
    $select_categories = $conn->prepare("SELECT * FROM `categorie` WHERE code = ?");
    $select_categories->execute([$update_id]);

    if ($select_categories->rowCount() > 0) {
        while ($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="pid" value="<?= $fetch_category['code']; ?>">
                <span>update name</span>
                <input type="text" name="nom" required class="box" maxlength="100" placeholder="enter category name" value="<?= $fetch_category['nom']; ?>">
                <div class="flex-btn">
                    <input type="submit" name="update" class="btn" value="update">
                    <a href="categories.php" class="option-btn">go back</a>
                </div>
            </form>
            <?php
        }
    } else {
        echo '<p class="empty">no category found!</p>';
    }
    ?>


</section>












<script src="../js/admin_script.js"></script>
   
</body>
</html>