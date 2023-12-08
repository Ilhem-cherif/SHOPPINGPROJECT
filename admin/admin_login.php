<?php
include '../components/connect.php';

session_start();

// Check if the form is submitted
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    // Check if "Remember Me" is checked
    $rememberMe = isset($_POST['remember']) ? true : false;

    $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
    $select_admin->execute([$name, $pass]);
    $row = $select_admin->fetch(PDO::FETCH_ASSOC);

    if($select_admin->rowCount() > 0){
        $_SESSION['admin_id'] = $row['id'];

        // Set cookies if "Remember Me" is checked
        if ($rememberMe) {
            setcookie('admin_name', $name, time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('admin_pass', $pass, time() + (86400 * 30), "/"); // Adjust expiration time as needed
        }else {
            // Unset cookies if "Remember Me" is not checked
            setcookie('admin_name', '', time() - 3600, "/");
            setcookie('admin_pass', '', time() - 3600, "/");
        }

        header('location:dashboard.php');
    } else {
        $message[] = 'Incorrect username or password!';
    }
}

// Check if cookies are set and populate the form fields
$rememberedName = isset($_COOKIE['admin_name']) ? $_COOKIE['admin_name'] : '';
$rememberedPass = isset($_COOKIE['admin_pass']) ? $_COOKIE['admin_pass'] : '';
// Logout logic - unset cookies and session
if (isset($_GET['logout'])) {
    setcookie('admin_name', '', time() - 3600, "/");
    setcookie('admin_pass', '', time() - 3600, "/");
    session_destroy();
    header('location: admin_login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/new_login_style.css">
</head>
<body>

<?php
if(isset($message)){
    foreach($message as $message){
        echo '
        <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<section class="login-form">
    <form action="" method="post">
        <h1 class="text-center">Login</h1>
        <div class="form-group">
            <div class="input-group">
                <input type="text" name="name" class="form-control" placeholder="Username" required="required" value="<?php echo $rememberedName; ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <input type="password" name="pass" class="form-control" placeholder="Password" required="required" value="<?php echo $rememberedPass; ?>">
            </div>
        </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block" name="submit">Log in</button>
        </div>
        <div class="bottom-action clearfix">
               <input type="checkbox" class="form-check-input" name="remember" id="remember" <?php echo isset($_COOKIE['admin_name']) ? 'checked' : ''; ?>>
               &nbsp; 
               <label class="form-check-label" for="remember">Remember me</label>
        </div>
    </form>
</section>
   
</body>
</html>
