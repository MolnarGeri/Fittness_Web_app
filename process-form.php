<?php
$userName=$_POST['userName'];
$password=$_POST['password'];
$email=$_POST['email'];

//var_dump($userName, $password, $email);

$host="localhost";
$db_name="fitneswebapp";
$username="root";
$db_password= "";

$conn=mysqli_connect(
    hostname:$host,
    username: $username,
    password: $db_password,
    database: $db_name
);

if (mysqli_connect_errno()){
    die("Connection error: ".mysqli_connect_error());
}

echo "<p>Connection succesfull</p>";



$sql="INSERT INTO user(name,emailAddress,password) VALUES(?,?,?)";

$stmt=mysqli_stmt_init($conn);



if (! mysqli_stmt_prepare($stmt, $sql)){
    die (mysqli_error($conn));
}


mysqli_stmt_bind_param($stmt,"sss",
                        $userName,
                        $email,
                        $password);



mysqli_stmt_execute($stmt);

echo "<p>Sikeres regisztráció</p>";


?>