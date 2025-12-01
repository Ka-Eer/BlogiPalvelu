<?php
session_start();
$isLoggedIn = isset($_SESSION['user_ID']);
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogi - Luo käyttäjä</title>
    <!--Bootstrap linkit-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="register.css">
</head>
<body id="Style">
    <!--Otsikko-->
    <header class="Otsikko1">
        <h1>Blogi</h1>
    </header>
    <!--Navbar-->
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Logo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Etusivu</a></li>
                    <li class="nav-item"><a class="nav-link" href="luo_blogi.php">Luo blogi</a></li>
                    <li class="nav-item"><a class="nav-link" href="galleria.php">Galleria</a></li>
                </ul>
                <!--Teeman vaihto nappi-->
                <button id="themeToggle" class="btn btn-outline-light ms-3">
                    Dark / Light
                </button>
                <!--Kirjautuminen / Käyttäjävalikko-->
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown ms-3">
                        <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="PHP/logout.php">Kirjaudu ulos</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light ms-3">Kirjaudu sisään</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <form method="post" action="PHP/register_send.php">
        <div class="container container-fluid" id="rekisterointiSivu">
            <h2 class="text-center mb-4">Luo uusi käyttäjä</h2>
            <div>
                <label class="form-label" for="registerUserName">Käyttäjänimi</label>
                <input class="form-control" name="registerUserName" id="registerUserName" required minlength="3" maxlength="50">
                <small class="form-text text-muted">Käyttäjänimen tulee olla 3-50 merkkiä pitkä</small>
            </div>
            <div>
                <label class="form-label" for="registerPass">Salasana</label>
                <input class="form-control" name="registerPass" id="registerPass" type="password" required minlength="6">
                <small class="form-text text-muted">Salasanan tulee olla vähintään 6 merkkiä pitkä</small>
            </div>
            <div>
                <label class="form-label" for="registerPassConfirm">Vahvista salasana</label>
                <input class="form-control" name="registerPassConfirm" id="registerPassConfirm" type="password" required>
            </div>
            <div>
                <button class="btn btn-primary" type="submit">Rekisteröidy</button>
            </div>
            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-link">Takaisin kirjautumiseen</a>
            </div>
        </div>
    </form>
    <script>
        //Teeman vaihto ominaisuus
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            document.body.classList.add("dark-theme");
        }
        document.getElementById("themeToggle").addEventListener("click", () => {
            document.body.classList.toggle("dark-theme");
            //Tallentaa käyttäjän valitseman teeman
            if (document.body.classList.contains("dark-theme")) {
                localStorage.setItem("theme", "dark");
            } else {
                localStorage.setItem("theme", "light");
            }
        });

        //Tarkista että salasanat täsmäävät
        document.querySelector('form').addEventListener('submit', function(e) {
            const pass = document.getElementById('registerPass').value;
            const passConfirm = document.getElementById('registerPassConfirm').value;
            
            if (pass !== passConfirm) {
                e.preventDefault();
                alert('Salasanat eivät täsmää!');
            }
        });
    </script>

</body>
</html>
