<?php
session_start();
// ha nincs bejelnkezve akkor a login.php-ra küldi
if(empty($_SESSION["username"]) ){
    header("location: logout.php");
    die("Nincs jogosultság a hozzáféréshez. Kérjük, jelentkezzen be.");
}
//ha nincs admin jogosultsága akkor a pofiljára küldi
if($_SESSION["is_admin"]==0){
    header("location: persondata.php");
    die("Nincs jogosultság a hozzáféréshez.");
}

$eror="";

$servername = "localhost";
$username = "root";  // a saját felhasználóneved
$password = "";      // a saját jelszavad
$dbname = "fitnessdb";


try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
}


if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submTraining']) && !empty($pdo)){
    $traningName=$_POST['traningName'];
    $traningCaloriesBurn=$_POST['traningCaloriesburn'];  
    try{
        $sqlFood="INSERT INTO training (trainingname,caloriesburn) VALUES(:traningName,:traningCaloriesBurn)";
        $traningCaloriesBurn=floatval($traningCaloriesBurn);
        $querryFood=$pdo->prepare($sqlFood);
        $querryFood->bindParam(":traningName",$traningName,PDO::PARAM_STR);
        $querryFood->bindParam(':traningCaloriesBurn',$traningCaloriesBurn, PDO::PARAM_STR);
        $querryFood->execute();


    }catch(PDOException $ex){
        $error="Hiba történt".$ex->getMessage();
    }
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php 
if(!empty($error)){
    echo "<p class='error'>$error</p>";
}

?>

<h1>Admin site</h1>


<h1>Edzés felvitele:</h1>

<form action="<?= htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
    <fieldset>
    <label id="traningName">Étel neve</label>
        <input type="text" name="traningName" id="traningName">
    <label id="traningCaloriesburn">Kalória értéke</label>
        <input type="number" step="0.01" name="traningCaloriesburn" id="traningCaloriesburn">
    
    <input type="submit" value="Mentés" name="submTraining">
</fieldset>
</form>

<p><a href="logout.php?logout">Kijelentkezés</a></p>
    
</body>
</html>