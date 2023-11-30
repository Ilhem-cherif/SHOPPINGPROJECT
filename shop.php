<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shop</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="search-form">
   <form action="" method="post">
      <input type="text" id="searchInput" name="search_box" placeholder="search here..." maxlength="100" class="box" required>
      <button type="submit" id="searchButton" class="fas fa-search" name="search_btn"></button>
   </form>
</section>


<section class="products">

   <h1 class="heading">latest products</h1>

   <div class="box-container" id="productContainer">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products`"); 
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>$</span><?= $fetch_product['price']; ?><span>/-</span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const productsContainer = document.getElementById('productContainer');

    // Use the input event for dynamic searching
    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.trim();

        if (searchTerm !== '') {
            // Perform AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'search_products.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const results = JSON.parse(xhr.responseText);
                    updateProductList(results);
                }
            };
            xhr.send('search=' + encodeURIComponent(searchTerm));
        } else {
            // If the search term is empty, show all products
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'search_products.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const results = JSON.parse(xhr.responseText);
                    updateProductList(results);
                }
            };
            xhr.send('search=');
        }
    });

    function updateProductList(products) {
        // Clear existing products
        productsContainer.innerHTML = '';

        if (products.length > 0) {
            // Add new products to the container
            products.forEach(function (product) {
                const box = createProductBox(product);
                productsContainer.appendChild(box);
            });
        } else {
            // Display a message when no products are found
            productsContainer.innerHTML = '<p class="empty">No products found!</p>';
        }
    }

    function createProductBox(product) {
        // Create HTML for a product box
        const box = document.createElement('form');
        box.setAttribute('action', '');
        box.setAttribute('method', 'post');
        box.classList.add('box');

        // Add the product details to the form
        box.innerHTML = `
        <input type="hidden" name="pid" value="${product.id}">
        <input type="hidden" name="name" value="${product.name}">
        <input type="hidden" name="price" value="${product.price}">
        <input type="hidden" name="image" value="${product.image_01}">
        <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
        <a href="quick_view.php?pid=${product.id}" class="fas fa-eye"></a>
        <img src="uploaded_img/${product.image_01}" alt="">
        <div class="name">${product.name}</div>
        <div class="flex">
            <div class="price"><span>$</span>${product.price}<span>/-</span></div>
            <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
        </div>
        <input type="submit" value="add to cart" class="btn" name="add_to_cart">
    `;

        return box;
    }
});

</script>

</body>
</html>
