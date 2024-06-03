<?php

   session_start();
   if(isset($_SESSION['user'])) header('Location:login.php');
   $error_message = '';

if ($_POST) {
    include('database/connection.php');
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    $query = 'SELECT * FROM users WHERE users.email="' . $username . '" AND users.password="' . $password . '"';

    $stmt = $conn->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Successful login logic
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $user=$stmt->FetchAll()[0];
        $_SESSION['user']=$user;

        header('Location:dashboard.php');
    } else {
        $error_message = 'Please make sure that your username and password are correct.';
    }
}

// Rest of the HTML code
?>


<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>IMS Login - Inventory Management System</title>

    <link rel="stylesheet" type="text/css" href="./css/login.css/login.css">
</head>
<body id="loginBody">
    <?php if(!empty($error_message)) { ?>
        <div id="errorMessage">
             <strong>Error:</strong> </p><?= $error_message ?> </p>
    </div>
    <?php } ?>
    <div class="container">
         <div class="loginHeader"> 
            <h1>IMS</h1>
            <p>Inventory Management System</p>
         </div>

         <div class="loginBody">
            <form action="login.php" method="POST">
                <div class="loginInputsContainer">

                    <label for="">Username</label>
                        <input placeholder="username" name="username" type="text" />
                   
                </div>

                <div class="loginInputsContainer">
                    <label for="">Password</label>
                        <input placeholder="password" name="password" type="password" />
                   
                </div>

                <div class="loginbuttoncontainer">
                    <button>login</button>
                </div>
            </form>

         </div>
    </div>
</body>
</html>