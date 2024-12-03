<?php
session_start();


if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Nincs jogosultság a hozzáféréshez. Kérjük, jelentkezzen be.");
   
    header('location:login.php');
    die("Nincs jogosultság a hozzáféréshez. Kérjük, jelentkezzen be.");
}

if($_SESSION['is_admin']==1){
    header('location:admin.php');
}

$userId = $_SESSION['user_id'];


$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "fitnessdb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis-kapcsolat hiba: " . $e->getMessage());
}


$name = '';
$birthdate = '';
$weight = '';
$height = '';
$gender = 0;
$weeklyTraining = '';
$goalWeight = '';
$BMI = '';
$bodyFatPercentage = '';
$FFM = '';
$BMR = '';
$TDEE = '';
$goalDate = '';


if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $birthdate = $_POST['birthdate'];
    $weight = (double)$_POST['weight'];
    $height = (double)$_POST['height'];
    $gender = (int)$_POST['gender'];  
    $weeklyTraining = $_POST['weekly_training'];
    $goalWeight = (double)$_POST['goal_weight'];

    
    $age = date_diff(date_create($birthdate), date_create('now'))->y;

    
    $heightInMeters = $height / 100;
    $BMI = $weight / ($heightInMeters * $heightInMeters);

    
    if ($gender == 0) { 
        $bodyFatPercentage = (1.20 * $BMI) + (0.23 * $age) - 16.2;
    } else { 
        $bodyFatPercentage = (1.20 * $BMI) + (0.23 * $age) - 5.4;
    }

    
    $FFM = (1 - ($bodyFatPercentage / 100)) * $weight;

   
    $BMR = 370 + (21.6 * $FFM);

    
    switch ($weeklyTraining) {
        case "1-3 óra/hét":
            $TDEE = $BMR * 1.2;
            break;
        case "4-6 óra/hét":
            $TDEE = $BMR * 1.35;
            break;
        case "6+ óra/hét":
            $TDEE = $BMR * 1.5;
            break;
        default:
            $TDEE = $BMR; 
    }

    
    $goal = abs($goalWeight - $weight);
    $goalCalories = $goal * 7700;
    $days = $goalCalories / 500;
    $goalDate = date('Y-m-d', strtotime("+$days days"));

   
    $checkStmt = $conn->prepare("SELECT * FROM persondatatbl WHERE user_id = ?");
    $checkStmt->execute([$userId]);
    $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
       
        $stmt = $conn->prepare("UPDATE persondatatbl SET name=?, birthdate=?, weight=?, height=?, gender=?, bodyfatpercentage=?, FFM=?, BMI=?, BMR=?, TDEE=?, weeklytraining=?, goalweight=?, goaldate=? WHERE user_id=?");
        $stmt->execute([$name, $birthdate, $weight, $height, $gender, $bodyFatPercentage, $FFM, $BMI, $BMR, $TDEE, $weeklyTraining, $goalWeight, $goalDate, $userId]);
        echo "<h1>Adatok sikeresen frissítve!</h1>";
    } else {
        
        $stmt = $conn->prepare("INSERT INTO persondatatbl (user_id, name, birthdate, weight, height, gender, bodyfatpercentage, FFM, BMI, BMR, TDEE, weeklytraining, goalweight, goaldate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $name, $birthdate, $weight, $height, $gender, $bodyFatPercentage, $FFM, $BMI, $BMR, $TDEE, $weeklyTraining, $goalWeight, $goalDate]);
        echo "<h1>Új adat sikeresen hozzáadva!</h1>";
    }
}


$stmt = $conn->prepare("SELECT * FROM persondatatbl WHERE user_id = ?");
$stmt->execute([$userId]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);


if ($result) {
   
    $name = $result['name'];
    $birthdate = $result['birthdate'];
    $weight = $result['weight'];
    $height = $result['height'];
    $gender = $result['gender'];
    $weeklyTraining = $result['weeklytraining'];
    $goalWeight = $result['goalweight'];

    
    $BMI = $result['BMI'];
    $bodyFatPercentage = $result['bodyfatpercentage'];
    $FFM = $result['FFM'];
    $BMR = $result['BMR'];
    $TDEE = $result['TDEE'];
    $goalDate = $result['goaldate'];
} else {
    echo "<h1>Nincs találat a felhasználó profiljára. Kérjük, töltse ki az adatokat!</h1>";
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Adatlap kitöltése</title>
    <link rel="stylesheet" href="persondata.css">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/png">
</head>
<body>
<header id="home">
    <nav>
        <div class="nav__bar">
            <div class="hero__container">
            <section class="hero">
            <div class="section__container hero__container">
            <p class="logo">HealthMap</p>
            </div>
            </section>
        </div>
            <ul class="nav__links">
                <li class="link"><a href="start.php">Főoldal</a></li>
                <li class="link"><a href="start.php">Adatlapom</a></li>
                <li class="link"><a href="fooddiary.php">Étkezésnapló</a></li>
                <li class="link"><a href="trainingdiary.php">Edzésnapló</a></li>
                <li class="link"><a href="logout.php">Kijelentkezés</a></li>
                <li class="link search">
                    <span><i class='bx bxs-face'></i></span>
                </li>
            </ul>
        </div>
    </nav>
</header>
    <div class="adatlap">
        <h2 class="colorcenter">Adatlap kitöltése</h2>
        <form method="POST" action="">
            <label for="name">Név:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>

            <label for="birthdate">Születési dátum:</label>
            <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($birthdate) ?>" required><br><br>

            <label for="weight">Súly (kg):</label>
            <input type="number" id="weight" name="weight" value="<?= htmlspecialchars($weight) ?>" step="0.1" required><br><br>

            <label for="height">Magasság (cm):</label>
            <input type="number" id="height" name="height" value="<?= htmlspecialchars($height) ?>" required><br><br>

            <label for="gender">Nem:</label>
            <select id="gender" name="gender" required>
                <option value="0" <?= $gender == 0 ? 'selected' : '' ?>>Férfi</option>
                <option value="1" <?= $gender == 1 ? 'selected' : '' ?>>Nő</option>
            </select><br><br>

            <label for="weekly_training">Havi edzés:</label>
            <select id="weekly_training" name="weekly_training" required>
                <option value="0-1 óra/hét">0-1 óra/hét</option>
                <option value="1-3 óra/hét" <?= $weeklyTraining == '1-3 óra/hét' ? 'selected' : '' ?>>1-3 óra/hét</option>
                <option value="4-6 óra/hét" <?= $weeklyTraining == '4-6 óra/hét' ? 'selected' : '' ?>>4-6 óra/hét</option>
                <option value="6+ óra/hét" <?= $weeklyTraining == '6+ óra/hét' ? 'selected' : '' ?>>6+ óra/hét</option>
            </select><br><br>

            <label for="goal_weight">Célsúly (kg):</label>
            <input type="number" id="goal_weight" name="goal_weight" value="<?= htmlspecialchars($goalWeight) ?>" step="0.1" required><br><br>

            <input type="submit" name="submit" value="Küldés">
        </form>
    </div>
    <div class= adatlap>
        <h2>Profil információk</h2>
        <table>
            <tr>
                <th>Név</th>
                <td><?= htmlspecialchars($name) ?></td>
            </tr>
            <tr>
                <th>Születési dátum</th>
                <td><?= htmlspecialchars($birthdate) ?></td>
            </tr>
            <tr>
                <th>Súly (kg)</th>
                <td><?= htmlspecialchars($weight) ?></td>
            </tr>
            <tr>
                <th>Magasság (cm)</th>
                <td><?= htmlspecialchars($height) ?></td>
            </tr>
            <tr>
                <th>Nem</th>
                <td><?= $gender == 0 ? 'Férfi' : 'Nő' ?></td>
            </tr>
            <tr>
                <th>Havi edzés</th>
                <td><?= htmlspecialchars($weeklyTraining) ?></td>
            </tr>
            <tr>
                <th>Célsúly (kg)</th>
                <td><?= htmlspecialchars($goalWeight) ?></td>
            </tr>
            <tr>
                <th>BMI</th>
                <td><?= htmlspecialchars($BMI) ?></td>
            </tr>
            <tr>
                <th>Testzsírszázalék (%)</th>
                <td><?= htmlspecialchars($bodyFatPercentage) ?></td>
            </tr>
            <tr>
                <th>Sovány testtömeg (FFM)</th>
                <td><?= htmlspecialchars($FFM) ?></td>
            </tr>
            <tr>
                <th>Alapanyagcsere (BMR)</th>
                <td><?= htmlspecialchars($BMR) ?></td>
            </tr>
            <tr>
                <th>Napi energiaszükséglet (TDEE)</th>
                <td><?= htmlspecialchars($TDEE) ?></td>
            </tr>
            <tr>
                <th>Cél dátuma</th>
                <td><?= htmlspecialchars($goalDate) ?></td>
            </tr>
        </table>
    </div>
    <section class="footer">
      <div class="section__container footer__container">
        <h4>HealthMap</h4>
        <div class="footer__socials">
          <span>
            <a href="#"><i class="ri-facebook-fill"></i></a>
          </span>
          <span>
            <a href="#"><i class="ri-instagram-fill"></i></a>
          </span>
          <span>
            <a href="#"><i class="ri-twitter-fill"></i></a>
          </span>
          <span>
            <a href="#"><i class="ri-linkedin-fill"></i></a>
          </span>
        </div>
        <p>
          HealtMap. Ébreszd fel a benned szunnyadó óriást és használd az erőt ami már most rendelkezésedre áll!
        </p>
        <ul class="footer__nav">
                <li class="link"><a href="start.php">Főoldal</a></li>
                <li class="link"><a href="start.php">Adatlapom</a></li>
                <li class="link"><a href="fooddiary.php">Étkezésnapló</a></li>
                <li class="link"><a href="trainingdiary.php">Edzésnapló</a></li>
                <li class="link"><a href="logout.php">Kijelentkezés</a></li>
        </ul>
      </div>
      <div class="footer__bar">
        Copyright © 2024 Szerveroldali programozás. Minden jog fenntartva.
      </div>
    </section>
</body>
</html>
