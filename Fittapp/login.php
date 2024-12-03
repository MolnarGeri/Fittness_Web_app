<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "fitnessdb";
$message = "";

try {
    $connDB = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $connDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit'])) {
            // Bejelentkezés logika
            if (empty($_POST['username']) || empty($_POST['password'])) {
                $message = '<label>All fields are required</label>';
            } else {
                $stmt = $connDB->prepare("SELECT id, username, is_admin, password FROM user WHERE username = :username");
                $stmt->execute([':username' => $_POST['username']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Jelszó ellenőrzése hashelés nélkül
                if ($user &&  password_verify($_POST['password'],$user['password'])  /*$_POST['password'] === $user['password']*/) {
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["is_admin"]=$user["is_admin"];
                   if($_SESSION["is_admin"]==0){
                    header("location: start.php");
                    }else{
                        header("location: admin.php");
                    }
                   
                    exit();
                } else {
                    $message = '<label>Invalid username or password</label>';
                }
            }
        }
        // Regisztráció logika
        elseif (isset($_POST['userName'], $_POST['email'], $_POST['password'])) {
            $userName = $_POST['userName'];
            $email = $_POST['email'];
            $passwordPlain = password_hash( $_POST['password'],PASSWORD_DEFAULT); // Jelszó mentése sima szövegként

            $stmt = $connDB->prepare("INSERT INTO user (userName, email, password) VALUES (:userName, :email, :password)");
            $stmt->execute([':userName' => $userName, ':email' => $email, ':password' => $passwordPlain]);

            if ($stmt->rowCount() > 0) {
                echo "<p>Registration successful!</p>";
            } else {
                echo "<p class='error'>Error during registration!</p>";
            }
        }
    }
} catch (PDOException $e) {
    $message = $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="loginstyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel="stylesheet">
    <title>Bejelentkezés</title>
</head>
<body>
    <header>
    <section class="hero">
      <div class="section__container hero__container">
        <p>HealthMap</p>
      </div>
    </section>
    </header>
    <div class="wrapper">
        <span class="bg-animate"></span>
        <span class="bg-animate2"></span>
        <div class="form-box login">
            <h2 class="animation" style="--i:0; --j:21;">Bejelentkezés</h2>
            <?php if (isset($message)) echo '<label class="text-danger">' . $message . ' </label>'; ?>
            <form action="" method="POST">
                <div class="input-box animation" style="--i:1; --j:22;">
                    <input type="text" name="username" required>
                    <label>Felhasználónév</label>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box animation" style="--i:2; --j:23;">
                    <input type="password" name="password" required>
                    <label>Jelszó</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn animation" name="submit" style="--i:3; --j:24;">Bejelentkezés</button>
                <div class="logreg-link animation" style="--i:4; --j:25;">
                    <p>Nem rendelkezik még fiókkal? <a href="#" class="register-link">Regisztálj</a></p>
                </div>
            </form>
        </div>

        <div class="info-text login">
            <h2 class="animation" style="--i:0; --j:20;">HealtMap</h2>
            <p class="animation" style="--i:1; --j:21;">Üdv újra! További tartalmakért kérjük, jelentkezzen be.</p>
        </div>

        <div class="form-box register">
            <h2 class="animation" style="--i:17; --j:0;">Regisztálj</h2>
            <form action="" method="POST">
                <div class="input-box animation" style="--i:18; --j:1;">
                    <input type="text" name="userName" required>
                    <label>Felhasználónév</label>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box animation" style="--i:19; --j:2;">
                    <input type="email" name="email" required>
                    <label>Email</label>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box animation" style="--i:20; --j:3;">
                    <input type="password" name="password" required>
                    <label>Jelszó</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn animation" style="--i:21; --j:4;">Regisztálj</button>
                <div class="logreg-link animation" style="--i:22; --j:5;">
                    <p>Rendelkezik már fiókkal? <a href="#" class="login-link">Bejelentkezés</a></p>
                </div>
            </form>
        </div>

        <div class="info-text register">
            <h2 class="animation" style="--i:17; --j:0;">HealtMap</h2>
            <p class="animation" style="--i:18; --j:1;">Üdvözöljük! További tartalmakért kérjük, jelentkezzen be.</p>
        </div>
    </div>

    <script src="loginscript.js"></script>
</body>
</html>
