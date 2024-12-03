<?php
session_start();

// Ellenőrizd, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    die("Nincs jogosultság a hozzáféréshez.");
}

$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="icon" href="assets/favicon-32x32.png" type="image/png">
    <link rel="stylesheet" href="startstyle.css" />
    <script src="https://unpkg.com/scrollreveal"></script>
    <title>Szerveroldali | HealthMap</title>
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
              <span><i class='bx bxs-face'></i></span>
            </li>
          </ul>
        </div>
      </nav>
      <div class="section__container header__container">
        <h1>Haladj a célod felé! Valósítsd meg az álmaidat!</h1>
        <h4>állítsd be az adatlapodat</h4>
        <button class="btn">
          <a href="persondata.php">Adatlap beállítása <i class="ri-arrow-right-line"></i></a>
        </button>
      </div>
    </header>

    <section class="about" id="about">
      <div class="section__container about__container">
        <div class="about__content">
          <h2 class="section__header">Rólunk</h2>
          <p class="section__subheader">
            Küldetésünk, hogy hozzásegítsünk álmaid testének eléréséhez, 
            és megmutassuk, hogy ez könnyebb, mint gondolnád! 
            Személyre szabottan felmérjük és kiértékeljük egészségi állapotodat, 
            hogy a legmegfelelőbb edzéstervet és táplálkozási tervet kínálhassuk Neked. 
            Garantáljuk, hogy a diétád során is energikusnak és vitálisnak érezd magad, miközben 
            folyamatosan közelebb kerülsz céljaidhoz. Gondoskodunk arról, hogy minden edzés 
            kihívásokkal teli, lendületes és motiváló legyen, a tested határait tiszteletben tartva. 
            Csatlakozz hozzánk, és kezdd el az utazást, amely teljesen új dimenzióba helyezi a fitness 
            és a fejlődésed művészetét.
          </p>
          <div class="about__flex">
            <div class="about__card">
              <h4>10+</h4>
              <p>Év tapasztalat</p>
            </div>
            <div class="about__card">
              <h4>5000+</h4>
              <p>Megírt étrend+edzésterv</p>
            </div>
            <div class="about__card">
              <h4>5000+</h4>
              <p>Elégedett sportoló</p>
            </div>
          </div>
          <button class="btn">
           <a href="persondata.php">Segíts kiértékelni<i class="ri-arrow-right-line"></i></a> 
          </button>
        </div>
        <div class="about__image">
          <img src="assets/aboutOne-3.jpg" alt="about" />
        </div>
      </div>
    </section>

    <section class="discover" id="discover">
      <div class="section__container discover__container">
        <h2 class="section__header">Fedezd fel szolgáltatásainkat!</h2>
        <p class="section__subheader">
          Első lépés megtétele a legnehezebb feladat, de mi segítünk benne!
        </p>
        <div class="discover__grid">
          <div class="discover__card">
            <div class="discover__image">
              <img src="assets/discoverOne.jpg" alt="discover" />
            </div>
            <div class="discover__card__content">
              <h4>Edzés Napló</h4>
              <p>
                Vezesd a napi edzésedet és a felhasznált kalóriákat! 
              </p>
              <button class="discover__btn">
                <a href="trainingdiary.php">Napló<i class="ri-arrow-right-line"></i></a>
              </button>
            </div>
          </div>
          <div class="discover__card">
            <div class="discover__image">
              <img src="assets/discoverTwo.jpg" alt="discover" />
            </div>
            <div class="discover__card__content">
              <h4>Támlálkozás napló</h4>
              <p>
                Mi kiszámoljuk, hogy mennyi kalóriát fogyaszthatsz és te vezetheted az elfogyasztott kalóriáidat. 
              </p>
              <button class="discover__btn">
               <a href="fooddiary.php"> Napló <i class="ri-arrow-right-line"></i></a>
              </button>
            </div>
          </div>
          <div class="discover__card">
            <div class="discover__image">
              <img src="assets/discoverThree.jpg" alt="discover" />
            </div>
            <div class="discover__card__content">
              <h4>Ideális testsúly</h4>
              <p>
                Állítsd be az álom testsúlyodat amit el szeretnél érni és mi végig vezetünk az ösvényen, hogy élvezni tudd életed legjobb formáját!
              </p>
              <button class="discover__btn">
                <a href="login.php">Érdekel <i class="ri-arrow-right-line"></i></a> 
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>


    
    <section class="hero">
      <div class="section__container hero__container">
        <p>HealthMap</p>
      </div>
    </section>

    

    <section class="contact" id="contact">
      <div class="section__container contact__container">
        <div class="contact__col">
          <h4>Kapcsolat</h4>
          <p>24 órán belül válaszolunk!</p>
        </div>
        <div class="contact__col">
          <div class="contact__card">
            <span><i class="ri-phone-line"></i></span>
            <h4>Hívj minket</h4>
            <h5>+36 30123456789</h5>
          </div>
        </div>
        <div class="contact__col">
          <div class="contact__card">
            <span><i class="ri-mail-line"></i></span>
            <h4>Keress e-mailben</h4>
          </div>
        </div>
      </div>
    </section>

    <section class="footer">
      <div class="section__container footer__container">
        <h4>HealthMap</h4>
        <div class="footer__socials">
        <span>
            <a href="https://www.facebook.com/davidlaid/?locale=hu_HU"><i class="ri-facebook-fill"></i></a>
          </span>
          <span>
            <a href="https://www.instagram.com/davidlaid/"><i class="ri-instagram-fill"></i></a>
          </span>
          <span>
            <a href="https://x.com/david_laid"><i class="ri-twitter-fill"></i></a>
          </span>
          <span>
            <a href="https://www.linkedin.com/posts/gymshark_as-you-may-have-seen-we-recently-appointed-activity-7035970565953703936-V481"><i class="ri-linkedin-fill"></i></a>
          </span>
        </div>
        <p>
          HealtMap. Ébreszd fel a benned szunnyadó óriást és használd az erőt ami már most rendelkezésedre áll!
        </p>
        <ul class="footer__nav">
                <li class="link"><a href="start.php">Főoldal</a></li>
                <li class="link"><a href="persondata.php">Adatlapom</a></li>
                <li class="link"><a href="fooddiary.php">Étkezésnapló</a></li>
                <li class="link"><a href="trainingdiary.php">Edzésnapló</a></li>
                <li class="link"><a href="logout.php">Kijelentkezés</a></li>
        </ul>
      </div>
      <div class="footer__bar">
        Copyright © 2024 Szerveroldali programozás. Minden jog fenntartva.
      </div>
    </section>

    <script src="main.js"></script>
  </body>
</html>
