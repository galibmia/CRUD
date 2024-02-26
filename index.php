<?php
require_once "inc//template//function.php";
session_start(['cookie_lifetime' => 200]);


if (!isset($_SESSION['loggedin'])) {
    header("location: /crud/auth.php");
    exit();
}

$info = '';
$task = $_GET['task'] ?? 'report';
$error = $_GET['error'] ?? '0';

if('edit' == $task){
    if(!hasPrivilege()){
        header('location: /crud/index.php?task=report');
        return; 
    }
}
// Seeding
if ('seed' == $task) {
    if(!isAdmin()){
    header('location: /crud/index.php?task=report');
    return;
    }
    seed();
    $info = "Seeding is Complete";
}

// Delete Segment
if ('delete' == $task) {
    if(!isAdmin()){
        header('location: /crud/index.php?task=report');
        return;
        }
    $id = filter_input(INPUT_GET, 'id');
    if ($id > 0) {
        deleteStudent($id);
        header('location: /crud/index.php?task=report');
    }
}



$fname = '';
$lname = '';
$roll = '';

if (isset($_POST['submit'])) {
    $fname = filter_input(INPUT_POST, 'fname');
    $lname = filter_input(INPUT_POST, 'lname');
    $roll = filter_input(INPUT_POST, 'roll');
    $id = filter_input(INPUT_POST, 'id');
    if ($id) { //Update section
        if ($fname != '' && $lname != '' && $roll != '') {
            $result = updateStudent($id, $fname, $lname, $roll);
            if ($result) {
                header('location: /crud/index.php?task=report');
            } else {
                $error = 1;
            }
        }
    } else {  //Add section
        if ($fname != '' && $lname != '' && $roll != '') {
            $result = addStudent($fname, $lname, $roll);
            if ($result) {
                header('location: /crud/index.php?task=report');
            } else {
                $error = 1;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD operations</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        body {
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="column column-60 column-offset-20">
                <h2>PHP CRUD</h2>
                <p>A simple project to perform CRUD operations</p>
                <div class="float-left">
                 
                <a href="/crud/index.php?task=report">All Students</a>
                <?php if (!isEditor() && (!isViewer())): ?>  
                    <a href="/crud/index.php?task=add"> | Add new Students</a> 
                    <a href="/crud/index.php?task=seed"> | Seed</a>
                    <?php endif; ?>   
                </div>
                <div class="float-right">
                    <?php

                    
                    if (!isset($_SESSION['loggedin'])) : ?>
                        <a href="/crud/auth.php">Log In</a>
                    <?php else : ?>
                        <a href="/crud/auth.php?logout=true&logout=true">Log Out (<?php echo $_SESSION['role']?>) </a>
                    <?php endif; ?>

                </div>
                <?php echo "<p>{$info}</p>" ?>
            </div>
        </div>
        <!-- Error message -->
        <?php if ('1' == $error) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <blockquote>Duplicate Roll Number</blockquote>
                </div>
            </div>
        <?php endif; ?>
        <!-- Report segment -->
        <?php if ('report' == $task) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Roll</th>
                                <?php if(!isViewer()): ?>
                                <th>Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php displayReport(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add task -->
        <?php if ('add' == $task) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <form method="post" action="/crud/index.php?task=add">
                        <fieldset>
                            <label for="fname">First Name</label>
                            <input type="text" placeholder="Enter Your First Name" id="fname" name="fname" value="<?php echo $fname ?>">
                            <label for="lname">Last Name</label>
                            <input type="text" placeholder="Enter Your Last Name" id="lname" name="lname" value="<?php echo $lname ?>">
                            <label for="roll">Roll</label>
                            <input type="number" placeholder="Enter Your Roll" id="roll" name="roll" value="<?php echo $roll ?>">
                            <button class="button-primary" type="submit" name="submit">Save</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Edit task -->
        <?php if ('edit' == $task) :
            $id = filter_input(INPUT_GET, 'id');
            $student = getStudent($id);
            if ($student) :
        ?>
                <div class="row">
                    <div class="column column-60 column-offset-20">
                        <form method="post">
                            <fieldset>
                                <input type="hidden" value="<?php echo $id; ?>" name="id">
                                <label for="fname">First Name</label>
                                <input type="text" placeholder="Enter Your First Name" id="fname" name="fname" value="<?php echo $student['fname'] ?>">
                                <label for="lname">Last Name</label>
                                <input type="text" placeholder="Enter Your Last Name" id="lname" name="lname" value="<?php echo $student['lname'] ?>">
                                <label for="roll">Roll</label>
                                <input type="number" placeholder="Enter Your Roll" id="roll" name="roll" value="<?php echo $student['roll'] ?>">
                                <button class="button-primary" type="submit" name="submit">Update</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
        <?php
            endif;
        endif;
        ?>
    </div>

</body>

</html>