<?php
session_start(['cookie_lifetime' => 200,]);
$_SESSION['time'] = time();

$error = '';

$userName = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

$fp = fopen("data/user.txt", "r");
if ($userName && $password) {
    $credentials_matched = false;
    while ($data = fgetcsv($fp)) {
        if ($data[0] === $userName && $data[1] === sha1($password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = $userName;
            $_SESSION['role'] = $data[2];
            $credentials_matched = true;
            header("location: /crud/index.php");
            exit();
        }
    }
    if (!$credentials_matched) {
        $error = 'Incorrect username or password.';
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['loggedin']);
    $_SESSION['user'] = false;
    $_SESSION['role'] = false;
    session_destroy();
    header("location: /crud/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <style>
        body {
            margin-top: 50px;
        }
        .row{
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .logout-btn{
            margin-top: 50px;
            width: 300px;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
    <div class="row" >
            <div class="column column-60 column-offset-20">
                
                <?php  if(!isset($_SESSION['loggedin'])){
                    echo "<h1> Hello, Please Login.</h1>";
                } ?>
            </div>
        </div>
                
        <?php if(!isset($_SESSION['loggedin'])): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <?php if(true == $error){
                    echo "<blockquote>Incorrect User Name of Password!</blockquote>";
                } ?>
            <form method="post">
                    <fieldset>
                        <label for="username">User Name</label>
                        <input type="text" placeholder="Enter Your User Name" id="username" name="username" autocomplete="off">
                        <label for="password">Password</label>
                        <input type="password" placeholder="Enter Your Password" id="password" name="password" autocomplete="off">
                        <button class="button-primary" type="submit" name="submit">Log In</button>
                    </fieldset>
                </form>
            </div>
        </div>
        <?php endif; ?>
        

    </div>

</body>

</html>

