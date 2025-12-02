<?php
session_start();

// Tietokantayhteys PDO:lla
try {
    $dbh = new PDO('mysql:host=localhost;dbname=blogipalvelu_db;charset=utf8mb4', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Tietokantayhteys epäonnistui: " . $e->getMessage());
}

// Haetaan lomakkeen tiedot
$userName = $_POST['loginUserName'] ?? '';
$pass = $_POST['loginPass'] ?? '';

// Virheilmoitukset
$error = '';
$success = false;

// Validointi
if (empty($userName) || empty($pass)) {
    $error = 'Käyttäjänimi ja salasana ovat pakollisia.';
} else {
    try {
        // Haetaan käyttäjä tietokannasta
        $stmt = $dbh->prepare("SELECT user_ID, kayttajaNimi, salasana FROM users WHERE kayttajaNimi = :username");
        $stmt->execute(['username' => $userName]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($pass, $user['salasana'])) {
            // Kirjautuminen onnistui
            $_SESSION['user_ID'] = $user['user_ID'];
            $_SESSION['username'] = $user['kayttajaNimi'];
            $success = true;
            
            // Ohjataan etusivulle
            header('Location: ../index.php');
            exit();
        } else {
            $error = 'Väärä käyttäjänimi tai salasana.';
        }
    } catch (PDOException $e) {
        $error = 'Kirjautuminen epäonnistui: ' . $e->getMessage();
    }
}

$dbh = null;
?>

<!DOCTYPE html>
<html lang="fi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Blogi - Kirjautuminen</title>
	<!--Bootstrap linkit-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<link rel="stylesheet" href="../login.css">
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
				<?php if (!empty($error)): ?>
					<div class="alert alert-danger" role="alert">
						<h4 class="alert-heading">Virhe!</h4>
						<p><?php echo htmlspecialchars($error); ?></p>
						<hr>
						<p class="mb-0"><a href="../login.php" class="alert-link">Yritä uudelleen</a></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>


</body>
</html>
