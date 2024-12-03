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


$stmt = $pdo->prepare("SELECT TDEE, goalweight, weight FROM persondatatbl WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$daily = 0;
if ($userData) {
    $TDEE = (int)$userData['TDEE'];
    $goalWeight = $userData['goalweight'];
    $weight = $userData['weight'];

   
    if ($goalWeight < $weight) {
        $daily = $TDEE - 500;
    } elseif ($goalWeight > $weight) {
        $daily = $TDEE + 500;
    } else {
        $daily = $TDEE;
    }
}


$date = date('Y-m-d');
$caloriesStmt = $pdo->prepare("SELECT SUM(calories) as totalEaten FROM mealtbl WHERE user_id = :user_id AND date = :date");
$caloriesStmt->execute(['user_id' => $userId, 'date' => $date]);
$caloriesData = $caloriesStmt->fetch(PDO::FETCH_ASSOC);
$totalEaten = $caloriesData['totalEaten'] ?? 0;


$daily -= $totalEaten;


$burnedCalories = $_SESSION['daily_calories_burned'] ?? 0; 
$daily += $burnedCalories; 


$foods = [];
$selectedFood = null;
$eatedCalories = $eatedCarb = $eatedProtein = $eatedFat = $gram = 0;
$mealSaved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['food_search'])) {
        $mealName = $_POST['food_search'];
        $searchStmt = $pdo->prepare("SELECT * FROM foodtbl WHERE mealname LIKE :mealname");
        $searchStmt->execute(['mealname' => "%$mealName%"]);
        $foods = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (isset($_POST['calculate_calories'])) {
        $foodData = json_decode($_POST['selected_food'], true);
        $gram = $_POST['gram'];

        $eatedCalories = ($foodData['calories'] / 100) * $gram;
        $eatedCarb = ($foodData['carb'] / 100) * $gram;
        $eatedProtein = ($foodData['protein'] / 100) * $gram;
        $eatedFat = ($foodData['fat'] / 100) * $gram;
        $selectedFood = $foodData;
    } elseif (isset($_POST['eat_button'])) {
        if (!empty($_POST['selected_food'])) {
            $foodData = json_decode($_POST['selected_food'], true);

            
            $eatedCalories = ($_POST['eated_calories'] ?? 0);
            $eatedCarb = ($_POST['eated_carb'] ?? 0);
            $eatedProtein = ($_POST['eated_protein'] ?? 0);
            $eatedFat = ($_POST['eated_fat'] ?? 0);

            
            $saveStmt = $pdo->prepare("INSERT INTO mealtbl (user_id, date, food, calories, carb, protein, fat) VALUES (:user_id, :date, :food, :calories, :carb, :protein, :fat)");
            $saveStmt->execute([
                'user_id' => $userId,
                'date' => $date,
                'food' => $foodData['mealname'],
                'calories' => $eatedCalories,
                'carb' => $eatedCarb,
                'protein' => $eatedProtein,
                'fat' => $eatedFat
            ]);

            
            $daily -= $eatedCalories;
            $mealSaved = true; 
            $selectedFood = null; 
        } else {
            echo "Hiba: Nincs kiválasztott étel";
        }
    }
}


$eatenMealsList = [];
$listStmt = $pdo->prepare("SELECT id, date, food, calories, carb, protein, fat FROM mealtbl WHERE user_id = :user_id ORDER BY date DESC");
$listStmt->execute(['user_id' => $userId]);
$eatenMealsList = $listStmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_meal'])) {
    $mealId = $_POST['delete_meal'];
    $deleteStmt = $pdo->prepare("DELETE FROM mealtbl WHERE id = :id");
    $deleteStmt->execute(['id' => $mealId]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=fooddiarystyle.css>
    <link rel="icon" href="assets/favicon-32x32.png" type="image/png">
    <title>Étkezésnapló</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        tr:hover {background-color: #f9f9f9;}
        .hidden { display: none; }
    </style>
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
    <div class= "etkezesnaplo">
    <h1>Étkezésnapló</h1>
    <p>Ma még ennyit ehetsz: <strong><?= $daily ?> kcal</strong></p>

    
    <form method="POST">
        <label>Mit ettél?</label>
        <input type="text" name="food_search">
        <button type="submit">Keresés</button>
    </form>
    </div>
    
     <div class = "megjelenites">
    <?php if (!empty($foods)): ?>
        <table border="1" id="food_table">
            <tr>
                <th>Étel neve</th>
                <th>Kalória (kcal / 100g)</th>
                <th>Szénhidrát (g)</th>
                <th>Fehérje (g)</th>
                <th>Zsír (g)</th>
            </tr>
            <?php foreach ($foods as $food): ?>
                <tr class="food_row" onclick="selectFood(<?= htmlspecialchars(json_encode($food)) ?>)">
                    <td><?= htmlspecialchars($food['mealname']) ?></td>
                    <td><?= htmlspecialchars($food['calories']) ?></td>
                    <td><?= htmlspecialchars($food['carb']) ?></td>
                    <td><?= htmlspecialchars($food['protein']) ?></td>
                    <td><?= htmlspecialchars($food['fat']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
            </div>

    
     <div class = "szamitas">
    <form method="POST" id="food_form" style="<?= $selectedFood ? '' : 'display:none;' ?>">
        <input type="hidden" name="selected_food" id="selected_food" value="<?= htmlspecialchars(json_encode($selectedFood)) ?>">
        <input type="hidden" name="eated_calories" value="<?= $eatedCalories ?>">
        <input type="hidden" name="eated_carb" value="<?= $eatedCarb ?>">
        <input type="hidden" name="eated_protein" value="<?= $eatedProtein ?>">
        <input type="hidden" name="eated_fat" value="<?= $eatedFat ?>">

        <label>Gramm:</label>
        <input type="number" name="gram" min="0" value="<?= $gram ?>" required>
        <button type="submit" name="calculate_calories">Kalória számítás</button>
        <button type="submit" name="eat_button">Megettem</button>
    </form>
            </div>
    
    <div class = "ertekek">
    <?php if ($eatedCalories > 0 && !$mealSaved): ?>  
        <h2>Kiszámolt értékek:</h2>
        <table border="1">
            <tr>
                <th>Étel neve</th>
                <th>Elfogyasztott kalória</th>
                <th>Szénhidrát</th>
                <th>Fehérje</th>
                <th>Zsír</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($selectedFood['mealname']) ?></td>
                <td><?= $eatedCalories ?> kcal</td>
                <td><?= $eatedCarb ?> g</td>
                <td><?= $eatedProtein ?> g</td>
                <td><?= $eatedFat ?> g</td>
            </tr>
        </table>
    <?php endif; ?>
    </div>

    
    <div class = "urlap">
    <h2>Elfogyasztott ételek:</h2>
    <table>
        <tr>
            <th>Dátum</th>
            <th>Étel</th>
            <th>Elfogyasztott kalória</th>
            <th>Szénhidrát</th>
            <th>Fehérje</th>
            <th>Zsír</th>
            <th>Akció</th>
        </tr>
        <?php if (!empty($eatenMealsList)): ?>
            <?php foreach ($eatenMealsList as $meal): ?>
                <tr>
                    <td><?= htmlspecialchars($meal['date']) ?></td>
                    <td><?= htmlspecialchars($meal['food']) ?></td>
                    <td><?= htmlspecialchars($meal['calories']) ?> kcal</td>
                    <td><?= htmlspecialchars($meal['carb']) ?> g</td>
                    <td><?= htmlspecialchars($meal['protein']) ?> g</td>
                    <td><?= htmlspecialchars($meal['fat']) ?> g</td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_meal" value="<?= $meal['id'] ?>">
                            <button type="submit" onclick="return confirm('Biztosan törölni szeretnéd ezt az étkezést?');">Törlés</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Nincs elfogyasztott étel rögzítve.</td>
            </tr>
        <?php endif; ?>
    </table>
        </div>

    <script>
        function selectFood(foodData) {
            document.getElementById('selected_food').value = JSON.stringify(foodData);
            document.getElementById('food_form').style.display = 'block';
            
            
            const rows = document.querySelectorAll('#food_table tr.food_row');
            rows.forEach(row => row.classList.add('hidden'));

            
            const selectedRow = Array.from(rows).find(row => 
                row.cells[0].innerText === foodData.mealname
            );
            if (selectedRow) {
                selectedRow.classList.remove('hidden');
            }

           
            const header = document.querySelector('#food_table tr:first-child');
            header.classList.remove('hidden');
        }

        
        <?php if ($daily < 0): ?>
            alert("Tájékoztatni szeretnénk, hogy elérted a napi limitedet, amit az alapján állítottunk be neked, hogy leghamarabb elérhesd a kitűzött célodat! Természetesen nincs gond ha átléped a kitűzött limitet, csak a program így nem lesz a leghatékonyabb.");
        <?php endif; ?>
    </script>
</body>
</html>
