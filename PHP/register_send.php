<?php
// Tietokantayhteys PDO:lla
try {
    $dbh = new PDO('mysql:host=localhost;dbname=blogipalvelu_db;charset=utf8mb4', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Tietokantayhteys epäonnistui: " . $e->getMessage());
}

// Haetaan lomakkeen tiedot
$userName = $_POST['registerUserName'] ?? '';
$pass = $_POST['registerPass'] ?? '';
$passConfirm = $_POST['registerPassConfirm'] ?? '';

// Virheilmoitukset
$error = '';
$success = false;

// Validointi
if (empty($userName) || empty($pass) || empty($passConfirm)) {
    $error = 'Kaikki kentät ovat pakollisia.';
} elseif (strlen($userName) < 3 || strlen($userName) > 50) {
    $error = 'Käyttäjänimen tulee olla 3-50 merkkiä pitkä.';
} elseif (strlen($pass) < 6) {
    $error = 'Salasanan tulee olla vähintään 6 merkkiä pitkä.';
} elseif ($pass !== $passConfirm) {
    $error = 'Salasanat eivät täsmää.';
} else {
    try {
        // Tarkistetaan onko käyttäjänimi jo olemassa
        $checkStmt = $dbh->prepare("SELECT kayttajaNimi FROM users WHERE kayttajaNimi = :username");
        $checkStmt->execute(['username' => $userName]);
        
        if ($checkStmt->rowCount() > 0) {
            $error = 'Käyttäjänimi on jo käytössä.';
        } else {
            // Hashataan salasana turvallisesti
            $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            
            // Lisätään käyttäjä tietokantaan
            $stmt = $dbh->prepare("INSERT INTO users (kayttajaNimi, salasana) VALUES (:username, :password)");
            $stmt->execute([
                'username' => $userName,
                'password' => $hashedPass
            ]);
            
            $success = true;
        }
    } catch (PDOException $e) {
        $error = 'Rekisteröinti epäonnistui: ' . $e->getMessage();
    }
}

$dbh = null;
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogi - Rekisteröinti</title>
    <!--Bootstrap linkit-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../register.css">
</head>
<body id="Style">
    <!--Otsikko-->
    <header class="Otsikko1">
        <h1>Blogi</h1>
    </header>
    <!--Navbar-->
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php"><img src="../Kuvat/Home.png" alt="Home" class="rounded-circle" width="48" height="48"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Etusivu</a></li>
                    <li class="nav-item"><a class="nav-link" href="../luo_blogi.php">Luo blogi</a></li>
                    <li class="nav-item"><a class="nav-link" href="../galleria.php">Galleria</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Rekisteröinti onnistui!</h4>
                        <p>Käyttäjätili on luotu onnistuneesti.</p>
                        <hr>
                        <p class="mb-0">Voit nyt <a href="../login.php" class="alert-link">kirjautua sisään</a>.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Virhe!</h4>
                        <p><?php echo htmlspecialchars($error); ?></p>
                        <hr>
                        <p class="mb-0"><a href="../register.php" class="alert-link">Yritä uudelleen</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
