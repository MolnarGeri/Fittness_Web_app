<?php
session_start();

// Ellenőrizd, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    die("Nincs jogosultság a hozzáféréshez.");
}

$userId = $_SESSION['user_id'];

// Adatbázis kapcsolat
$servername = "localhost";
$username = "root";  // a saját felhasználóneved
$password = "";      // a saját jelszavad
$dbname = "fitnessdb";

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Az űrlap értékeinek inicializálása
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

// Beküldés ellenőrzése
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $birthdate = $_POST['birthdate'];
    $weight = (double)$_POST['weight'];
    $height = (double)$_POST['height'];
    $gender = (int)$_POST['gender'];  // 0 vagy 1
    $weeklyTraining = $_POST['weekly_training'];
    $goalWeight = (double)$_POST['goal_weight'];

    // Kor kiszámítása
    $age = date_diff(date_create($birthdate), date_create('now'))->y;

    // BMI kiszámítása
    $heightInMeters = $height / 100;
    $BMI = $weight / ($heightInMeters * $heightInMeters);

    // Testzsírszázalék kiszámítása
    if ($gender == 0) { // Férfi
        $bodyFatPercentage = (1.20 * $BMI) + (0.23 * $age) - 16.2;
    } else { // Nő
        $bodyFatPercentage = (1.20 * $BMI) + (0.23 * $age) - 5.4;
    }

    // Sovány testtömeg (FFM)
    $FFM = (1 - ($bodyFatPercentage / 100)) * $weight;

    // Alapanyagcsere (BMR)
    $BMR = 370 + (21.6 * $FFM);

    // Napi energiaszükséglet (TDEE)
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
            $TDEE = $BMR; // Alapértelmezett
    }

    // Célsúly elérése
    $goal = abs($goalWeight - $weight);
    $goalCalories = $goal * 7700;
    $days = $goalCalories / 500;
    $goalDate = date('Y-m-d', strtotime("+$days days"));

    // SQL lekérdezés frissítése
    $stmt = $conn->prepare("UPDATE persondatatbl SET name=?, birthdate=?, weight=?, height=?, gender=?, bodyfatpercentage=?, FFM=?, BMI=?, BMR=?, TDEE=?, weeklytraining=?, goalweight=?, goaldate=? WHERE user_id=?");

    // Adatok frissítése az adatbázisban
    $stmt->execute([$name, $birthdate, $weight, $height, $gender, $bodyFatPercentage, $FFM, $BMI, $BMR, $TDEE, $weeklyTraining, $goalWeight, $goalDate, $userId]);

    if ($stmt) {
        echo "<h1>Adatok sikeresen mentve!</h1>";
    } else {
        echo "<h1>Hiba történt az adatok mentése közben.</h1>";
    }
}

// Profil adatainak lekérdezése
$stmt = $conn->prepare("SELECT * FROM persondatatbl WHERE user_id = ?");
$stmt->execute([$userId]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Ellenőrzés
if ($result) {
    // Az űrlap értékeinek beállítása a lekérdezett adatokra
    $name = $result['name'];
    $birthdate = $result['birthdate'];
    $weight = $result['weight'];
    $height = $result['height'];
    $gender = $result['gender'];
    $weeklyTraining = $result['weeklytraining'];
    $goalWeight = $result['goalweight'];

    // Számított adatok beállítása
    $BMI = $result['BMI'];
    $bodyFatPercentage = $result['bodyfatpercentage'];
    $FFM = $result['FFM'];
    $BMR = $result['BMR'];
    $TDEE = $result['TDEE'];
    $goalDate = $result['goaldate'];
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Adatlap kitöltése</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Adatlap kitöltése</h2>
    <form method="POST" action="">
        <label for="name">Név:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>

        <label for="birthdate">Születési dátum:</label>
        <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($birthdate) ?>" required><br><br>

        <label for="weight">Súly (kg):</label>
        <input type="number" id="weight" name="weight" value="<?= htmlspecialchars($weight) ?>" step="0.1" required><br><br>

        <label for="height">Magasság (cm):</label>
        <input type="number" id="height" name="height" value="<?= htmlspecialchars($height) ?>" step="0.1" required><br><br>

        <label for="gender">Nem:</label>
        <select id="gender" name="gender" required>
            <option value="0" <?= $gender == 0 ? 'selected' : '' ?>>Fiú</option>
            <option value="1" <?= $gender == 1 ? 'selected' : '' ?>>Lány</option>
        </select><br><br>

        <label for="weekly_training">Heti sportolási idő:</label>
        <select id="weekly_training" name="weekly_training" required>
            <option value="1-3 óra/hét" <?= $weeklyTraining == "1-3 óra/hét" ? 'selected' : '' ?>>1-3 óra/hét</option>
            <option value="4-6 óra/hét" <?= $weeklyTraining == "4-6 óra/hét" ? 'selected' : '' ?>>4-6 óra/hét</option>
            <option value="6+ óra/hét" <?= $weeklyTraining == "6+ óra/hét" ? 'selected' : '' ?>>6+ óra/hét</option>
        </select><br><br>

        <label for="goal_weight">Célsúly (kg):</label>
        <input type="number" id="goal_weight" name="goal_weight" value="<?= htmlspecialchars($goalWeight) ?>" step="0.1" required><br><br>

        <button type="submit" name="submit">Beküldés</button>
    </form>

    <?php if ($result): ?>
        <h2>Felhasználói profil adatai:</h2>
        <table>
            <tr>
                <th>Név</th>
                <td><?= htmlspecialchars($result['name']) ?></td>
            </tr>
            <tr>
                <th>Születési dátum</th>
                <td><?= htmlspecialchars($result['birthdate']) ?></td>
            </tr>
            <tr>
                <th>Súly (kg)</th>
                <td><?= htmlspecialchars($result['weight']) ?></td>
            </tr>
            <tr>
                <th>Magasság (cm)</th>
                <td><?= htmlspecialchars($result['height']) ?></td>
            </tr>
            <tr>
                <th>Nem</th>
                <td><?= htmlspecialchars($result['gender']) == 0 ? 'Fiú' : 'Lány' ?></td>
            </tr>
            <tr>
                <th>Heti sportolási idő</th>
                <td><?= htmlspecialchars($result['weeklytraining']) ?></td>
            </tr>
            <tr>
                <th>Célsúly (kg)</th>
                <td><?= htmlspecialchars($result['goalweight']) ?></td>
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
                <th>Sovány testtömeg (kg)</th>
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
                <th>Cél dátum</th>
                <td><?= htmlspecialchars($goalDate) ?></td>
            </tr>
        </table>
    <?php else: ?>
        <h1>Nincs találat a felhasználó profiljára.</h1>
    <?php endif; ?>

<a href="fooddiary.php">
            <button>Napló</button>
</a>
</body>
</html>



