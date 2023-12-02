<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}else{
  $user_id = '';
};
if(isset($_GET['delete'])){
  $delete_id = $_GET['delete'];
  $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
  $delete_message->execute([$delete_id]);
  header('location:user_messages.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Messages</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="contacts">

        <h1 class="heading">Your Messages</h1>

        <div class="box-container">
            <?php
            $select_messages = $conn->prepare("SELECT * FROM `messages` WHERE user_id = ?");
            $select_messages->execute([$user_id]);
            if($select_messages->rowCount() > 0){
              while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
            ?>
            <div class="box">
              <p> user id : <span><?= $fetch_message['user_id']; ?></span></p>
              <p> name : <span><?= $fetch_message['name']; ?></span></p>
              <p> email : <span><?= $fetch_message['email']; ?></span></p>
              <p> number : <span><?= $fetch_message['number']; ?></span></p>
              <p> message : <span><?= $fetch_message['message'] ;?></span></p>
              <p> admin_reply : <span><?= $fetch_message['admin_reply'] ;?></span></p>
            <a href="user_messages.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('delete this message?');" class="delete-btn">delete</a>
            </div>
            <?php
                  }
              }else{
                  echo '<p class="empty">you have no messages</p>';
              }
            ?>
        </div>

    </section>

    <?php include 'components/footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>
