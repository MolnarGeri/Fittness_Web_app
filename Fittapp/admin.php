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



if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['submFood']) && !empty($pdo)){
    $foodName=$_POST['foodName'];
    $foodCalories=$_POST['foodCalories'];
    $foodCarb=$_POST['foodCarb'];
    $foodProtein=$_POST['foodProtein'];
    $foodFat=$_POST['foodFat'];
    try{
        $foodCalories=floatval($foodCalories);
        $foodCarb=floatval($foodCarb);
        $foodProtein=floatval($foodProtein);
        $foodFat=floatval($foodFat);

        echo "<p>$foodCalories</p>";
        echo "<p>$foodCarb</p>";
        $sqlFood='INSERT INTO foodtbl (mealname, calories, carb, protein, fat) VALUES(:mealname,:calories,:carb,:protein,:fat)';
        $queryFood=$pdo->prepare($sqlFood);
        $queryFood->bindValue(':mealname',$foodName,PDO::PARAM_STR);
        $queryFood->bindValue(':calories',$foodCalories,PDO::PARAM_STR);
        $queryFood->bindValue(':carb',$foodCarb,PDO::PARAM_STR);
        $queryFood->bindValue(':protein',$foodProtein,PDO::PARAM_STR);
        $queryFood->bindValue(':fat',$foodFat,PDO::PARAM_STR);
        $queryFood->execute();



    }catch(PDOException $ex){
        $error="Adatbéisu hiba történt:".$ex->getMessage();
        //die();
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




<h1>Étel felvitel</h1>

<form action="<?=htmlspecialchars(trim($_SERVER['PHP_SELF']))?>" method="post">
    <fieldset> Étel felvitel
        <label for="foodName">Név</label>
        <input type="text" name="foodName" id="foodName">
        <label for="foodCalories">Kalória</label>
        <input type="number" step="0.01" name="foodCalories" id="foodCalories">
        <label for="foodCarb">Carb</label>
        <input type="number" step="0.01" name="foodCarb" id="foodCarb">
        <label for="foodProtein">Protein</label>
        <input type="number" step="0.01" name="foodProtein" id="foodProtein">
        <label for="foodFat">Zsír</label>
        <input type="number" step="0.01" name="foodFat" id="foodFat">

        <input type="submit" value="Mentés" name="submFood">

    </fieldset>
</form>

<p><a href="logout.php?logout">Kijelentkezés</a></p>
    
</body>
</html>