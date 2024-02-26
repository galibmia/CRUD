<?php
define('DB_name', 'C:\\Projects\\php\\php_practice\\crud/data/db.txt');

function seed()
{
    $students = array(
        array(
            'id' => 1,
            'fname' => 'Galib',
            'lname' => 'Mia',
            'roll' => 15
        ),
        array(
            'id' => 2,
            'fname' => 'Sadia',
            'lname' => 'Sultana',
            'roll' => 12
        ),
        array(
            'id' => 3,
            'fname' => 'Afroza',
            'lname' => 'Akter',
            'roll' => 13
        ),
        array(
            'id' => 4,
            'fname' => 'Abu',
            'lname' => 'Saleh',
            'roll' => 16
        ),
        array(
            'id' => 5,
            'fname' => 'Rostom',
            'lname' => 'Ali',
            'roll' => 18
        )
    );

    $data = serialize($students);
    file_put_contents(DB_name, $data, LOCK_EX);
}

function displayReport()
{
    $serializedData = file_get_contents(DB_name);
    $students = unserialize($serializedData);
    foreach ($students as $student) {
        echo "<tr>";
        printf("<td>%s %s</td>", $student['fname'], $student['lname']);
        printf("<td>%s</td>", $student['roll']);
        $id = $student['id'];
        if(isAdmin()){
        printf("<td><a href='/crud/index.php?task=edit&id={$id}'>Edit</a> | <a class='delete' href='/crud/index.php?task=delete&id={$id}'>Delete</a></td>");
        }elseif(isEditor()){
            printf("<td><a href='/crud/index.php?task=edit&id={$id}'>Edit</a></td>");
        }
        echo "</tr>";
    }
}


function addStudent($fname, $lname, $roll)
{
    $found = false;
    $serializedData = file_get_contents(DB_name);
    $students = unserialize($serializedData);
    foreach ($students as $_student) {
        if ($_student['roll'] == $roll) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $newID = getMax($students) ?? 1;
        $student = array(
            'id' => $newID,
            'fname' => $fname,
            'lname' => $lname,
            'roll' => $roll
        );
        array_push($students, $student);
        $data = serialize($students);
        file_put_contents(DB_name, $data, LOCK_EX);
        return true;
    }
    return false;
}
function getStudent($id)
{
    $serializedData = file_get_contents(DB_name);
    $students = unserialize($serializedData);
    foreach ($students as $student) {
        if ($student['id'] == $id) {
            return $student;
        }
    }
    return false;
}


function updateStudent($id, $fname, $lname, $roll)
{
    $found = false;
    $serializedData = file_get_contents(DB_name);
    $students = unserialize($serializedData);

    foreach ($students as $_student) {
        if ($_student['roll'] == $roll && $_student['id'] != $id){
            $found = true;
            break;
        }
    }
    if (!$found) {
        $students[$id - 1]['fname'] = $fname;
        $students[$id - 1]['lname'] = $lname;
        $students[$id - 1]['roll'] = $roll;
        $data = serialize($students);
        file_put_contents(DB_name, $data, LOCK_EX);
        return true;
    }
    return false;
}


function deleteStudent($id){
    $serializedData = file_get_contents(DB_name);
    $students = unserialize($serializedData);
    foreach ($students as $offset=>$student) {
        if ($student['id'] == $id) {
            unset($students[$offset]);
        }
    }
    $data = serialize($students);
    file_put_contents(DB_name, $data, LOCK_EX);

}
function getMax($students){
    $ids = array_column($students, 'id');
    if(!empty($ids)){
        $maxId = max($ids);
        return $maxId + 1;
    }
    return 1;
}

function isAdmin(){ 
    return ('admin' == $_SESSION['role']);
}

function isEditor(){ 
   
    return ('editor' == $_SESSION['role']);
}
function isViewer(){ 
   
    return ('viewer' == $_SESSION['role']);
}

function hasPrivilege(){
    return (isAdmin() || isEditor());
}