<?php
$dbh = mysqli_connect('localhost', 'root', '', 'blogipalvelu_db');
if (!$dbh) {
 die("Unable to connect to MySQL: " . mysqli_connect_error());
}

$userName = $_POST['loginUserName'];
$pass = $_POST['loginPass'];
$sqlCheck = mysqli_query($dbh, "SELECT * FROM users");
$sql = "INSERT INTO users (`kayttajaNimi`, `salasana`) VALUES ('$userName', '$pass')";

?>

<!DOCTYPE html>
<html lang="fi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Blogi</title>
	<!--Bootstrap linkit-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<link rel="stylesheet" href="login.css">
</head>
<body>
	<!--Otsikko-->
	<header class="Otsikko1">
		<h1>Blogi</h1>
	</header>
	<!--Navbar-->
	<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
		<div class="container-fluid">
			<a class="navbar-brand" href="index.html">Logo</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-auto">
					<li class="nav-item"><a class="nav-link" href="index.html">Etusivu</a></li>
					<li class="nav-item"><a class="nav-link" href="blogi.html">Blogi</a></li>
					<li class="nav-item"><a class="nav-link" href="galleria.html">Galleria</a></li>
					<li class="nav-item"><a class="nav-link active" href="login.html">Kirjaudu sisään</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<?php
	if (mysqli_query($dbh, $sql)) {
		echo "Käyttäjä luotu";
		} else {
			echo "Error: " . mysqli_error($dbh);
		}
	?>


</body>
</html>
