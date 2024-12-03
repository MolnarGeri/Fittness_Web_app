<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Nincs jogosultság a hozzáféréshez.");
}

$userId = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "fitnessdb";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
}

// Napi kalóriák összesítése
$date = date('Y-m-d');
$caloriesSumStmt = $pdo->prepare("
    SELECT SUM(caloriesburned) as totalBurned
    FROM trainingeventtbl
    WHERE user_id = :user_id AND DATE(date) = :date
");
$caloriesSumStmt->execute(['user_id' => $userId, 'date' => $date]);
$caloriesData = $caloriesSumStmt->fetch(PDO::FETCH_ASSOC);
$totalBurned = $caloriesData['totalBurned'] ?? 0;

// Mentés session-be
$_SESSION['daily_calories_burned'] = $totalBurned;

// Edzés keresése és kiválasztás kezelése
$trainings = [];
$selectedTraining = null;
$burnedCalories = $duration = 0;
$trainingSaved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['training_search'])) {
        $trainingName = $_POST['training_search'];
        $searchStmt = $pdo->prepare("SELECT * FROM training WHERE trainingname LIKE :trainingname");
        $searchStmt->execute(['trainingname' => "%$trainingName%"]);
        $trainings = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_POST['calculate_calories'])) {
        $trainingData = json_decode($_POST['selected_training'], true);
        $duration = $_POST['duration'];

        $burnedCalories = ($trainingData['caloriesburn']) * $duration;
        $selectedTraining = $trainingData;
    } elseif (isset($_POST['train_button'])) {
        if (!empty($_POST['selected_training'])) {
            $trainingData = json_decode($_POST['selected_training'], true);

            $burnedCalories = $_POST['burned_calories'] ?? 0;
            $duration = $_POST['duration'] ?? 0;

            // Adatok mentése a trainingeventtbl táblába
            $saveStmt = $pdo->prepare("
                INSERT INTO trainingeventtbl (user_id, date, training, duration, caloriesburned)
                VALUES (:user_id, :date, :training, :duration, :caloriesburned)
            ");
            $saveStmt->execute([
                'user_id' => $userId,
                'date' => date('Y-m-d H:i:s'),
                'training' => $trainingData['trainingname'], // `trainingname` a `training` táblából
                'duration' => $duration,
                'caloriesburned' => $burnedCalories
            ]);

            $trainingSaved = true;
            $selectedTraining = null;

            // Napi kalóriák újraszámítása
            $caloriesSumStmt->execute(['user_id' => $userId, 'date' => $date]);
            $caloriesData = $caloriesSumStmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['daily_calories_burned'] = $caloriesData['totalBurned'] ?? 0;
        } else {
            echo "Hiba: Nincs kiválasztott edzés";
        }
    }
}

// Elvégzett edzések listázása
$completedTrainingsList = [];
$listStmt = $pdo->prepare("
    SELECT id, date, training, duration, caloriesburned 
    FROM trainingeventtbl 
    WHERE user_id = :user_id 
    ORDER BY date DESC
");
$listStmt->execute(['user_id' => $userId]);
$completedTrainingsList = $listStmt->fetchAll(PDO::FETCH_ASSOC);

// Elvégzett edzés törlése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_training'])) {
    $trainingId = $_POST['delete_training'];
    $deleteStmt = $pdo->prepare("DELETE FROM trainingeventtbl WHERE id = :id");
    $deleteStmt->execute(['id' => $trainingId]);

    // Napi kalóriák újraszámítása
    $caloriesSumStmt->execute(['user_id' => $userId, 'date' => $date]);
    $caloriesData = $caloriesSumStmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['daily_calories_burned'] = $caloriesData['totalBurned'] ?? 0;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edzésnapló</title>
    <link rel="stylesheet" href="trainingdiary.css" />
    <link rel="icon" href="assets/favicon-32x32.png" type="image/png">
</head>
<body>
    <header id="home">
      <nav>
        <div class="nav__bar">
          <div class="nav__logo"><a href="#">HealthMap</a></div>
          <ul class="nav__links">
                <li class="link"><a href="start.php">Főoldal</a></li>
                <li class="link"><a href="persondata.php">Adatlapom</a></li>
                <li class="link"><a href="fooddiary.php">Étkezésnapló</a></li>
                <li class="link"><a href="trainingdiary.php">Edzésnapló</a></li>
                <li class="link"><a href="logout.php">Kijelentkezés</a></li>
                <li class="link search">
            <li class="link search">
              <span><i class='bx bxs-face'></i></span>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <div class="urlap">
    <h1>Edzésnapló</h1>
    <p>Ma elégetett kalóriák: <strong><?= $_SESSION['daily_calories_burned'] ?> kcal</strong></p>

    
    <form method="POST">
        <label >Milyen edzést végeztél?</label>
        <input type="text" name="training_search">
        <button type="submit">Keresés</button>
    </form>

    
    <?php if (!empty($trainings)): ?>
        <table border="1" id="training_table">
            <tr>
                <th>Edzés neve</th>
                <th>Elégetett kalória (kcal / óra)</th>
            </tr>
            <?php foreach ($trainings as $training): ?>
                <tr class="training_row" onclick="selectTraining(<?= htmlspecialchars(json_encode($training)) ?>)">
                    <td><?= htmlspecialchars($training['trainingname']) ?></td>
                    <td><?= htmlspecialchars($training['caloriesburn']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    
    <form method="POST" id="training_form" style="<?= $selectedTraining ? '' : 'display:none;' ?>">
        <input type="hidden" name="selected_training" id="selected_training" value="<?= htmlspecialchars(json_encode($selectedTraining)) ?>">
        <input type="hidden" name="burned_calories" value="<?= $burnedCalories ?>">

        <label>Időtartam (perc):</label>
        <input type="number" name="duration" min="0" value="<?= $duration ?>" required>
        <button type="submit" name="calculate_calories">Kalória számítás</button>
        <button type="submit" name="train_button">Edzettem</button>
    </form>

    
    <?php if ($burnedCalories > 0 && !$trainingSaved): ?>
        <h2>Kiszámolt kalóriák:</h2>
        <table border="1">
            <tr>
                <th>Edzés neve</th>
                <th>Időtartam</th>
                <th>Elégetett kalória</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($selectedTraining['trainingname']) ?></td>
                <td><?= $duration ?> perc</td>
                <td><?= $burnedCalories ?> kcal</td>
            </tr>
        </table>
    <?php endif; ?>

    
    <h2>Elvégzett edzések:</h2>
    <table>
        <tr>
            <th>Dátum</th>
            <th>Edzés</th>
            <th>Időtartam</th>
            <th>Elégetett kalória</th>
            <th>Akció</th>
        </tr>
        <?php if (!empty($completedTrainingsList)): ?>
            <?php foreach ($completedTrainingsList as $training): ?>
                <tr>
                    <td><?= htmlspecialchars($training['date']) ?></td>
                    <td><?= htmlspecialchars($training['training']) ?></td>
                    <td><?= htmlspecialchars($training['duration']) ?> perc</td>
                    <td><?= htmlspecialchars($training['caloriesburned']) ?> kcal</td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_training" value="<?= $training['id'] ?>">
                            <button type="submit" onclick="return confirm('Biztosan törölni szeretnéd ezt az edzést?');">Törlés</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Nincs rögzített edzés.</td>
            </tr>
        <?php endif; ?>
    </table>
    </div>
    <script>
        function selectTraining(trainingData) {
            document.getElementById('selected_training').value = JSON.stringify(trainingData);
            document.getElementById('training_form').style.display = 'block';

            const rows = document.querySelectorAll('#training_table tr.training_row');
            rows.forEach(row => row.classList.add('hidden'));

            const selectedRow = Array.from(rows).find(row =>
                row.cells[0].innerText === trainingData.trainingname
            );
            if (selectedRow) {
                selectedRow.classList.remove('hidden');
            }

            const header = document.querySelector('#training_table tr:first-child');
            header.classList.remove('hidden');
        }
    </script>
</body>
</html>
