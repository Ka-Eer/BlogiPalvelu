<?php
session_start();
$isLoggedIn = isset($_SESSION['user_ID']);
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="luo_blogi.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luo blogi - Blogipalvelu</title>
</head>
<body id="Style">
    <header class="Header_Top">
        <h1 class="text-center">Luo blogi</h1>
    </header>
    <!-- navigaatio -->
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="Kuvat/Home.png" alt="Home" class="rounded-circle" width="48" height="48"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Etusivu</a></li>
                    <li class="nav-item"><a class="nav-link active" href="luo_blogi.php">Luo blogi</a></li>
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

    <!--Täytettävät kohdat-->
    <div id="postMessage" style="display:none; margin: 20px auto; max-width: 800px;"></div>
    
    <form action="PHP/blog_send.php" class="mx-auto" method="post" id="BlogForm" enctype="multipart/form-data">

        <br>
        <input class="form-control bg-light" type="text" name="blogTextTitle"  id="blogTextTitle" placeholder="Blogin Otsikko" required maxlength="75">
        <br>
        <textarea class="form-control bg-light" name="blogText" id="blogText" placeholder="Kirjoita blogiteksti tähän" required></textarea>
        <br>
        <label class="form-label" for="blogImg">Lisää Kuva (Valinnainen)</label>
        <br>
        <input class="form-control" type="file" accept="image/*" name="blogImg" id="blogImg">
        <div class="form-text">Maksimikoko: 15 MB.</div>
        <div class="form-control" id="checkBoxForm">
            <label class="form-label mb-3">Valitse tagit (Valinnainen)</label>
            <div class="blogiTagContainer">
                <!-- Piilotetut checkboxit formia varten -->
                <input type="checkbox" name="tags[]" id="blogTag1" value="1" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag2" value="2" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag3" value="3" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag4" value="4" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag5" value="5" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag6" value="6" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag7" value="7" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag8" value="8" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag9" value="9" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag10" value="10" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag11" value="11" style="display:none">
                <input type="checkbox" name="tags[]" id="blogTag12" value="12" style="display:none">
                
                <!-- Näkyvät badge-painikkeet -->
                <span class="badge badge-tag-1 tag-badge" data-tag="1" role="button" tabindex="0">Pelit</span>
                <span class="badge badge-tag-2 tag-badge" data-tag="2" role="button" tabindex="0">Matkustaminen</span>
                <span class="badge badge-tag-3 tag-badge" data-tag="3" role="button" tabindex="0">Teknologia & Internet</span>
                <span class="badge badge-tag-4 tag-badge" data-tag="4" role="button" tabindex="0">Oppiminen & Itsekehitys</span>
                <span class="badge badge-tag-5 tag-badge" data-tag="5" role="button" tabindex="0">Ruoka & Juoma</span>
                <span class="badge badge-tag-6 tag-badge" data-tag="6" role="button" tabindex="0">Hyvinvointi & Elämäntyyli</span>
                <span class="badge badge-tag-7 tag-badge" data-tag="7" role="button" tabindex="0">Luovuus & Kulttuuri</span>
                <span class="badge badge-tag-8 tag-badge" data-tag="8" role="button" tabindex="0">Työ & Ura</span>
                <span class="badge badge-tag-9 tag-badge" data-tag="9" role="button" tabindex="0">Koti & Arki</span>
                <span class="badge badge-tag-10 tag-badge" data-tag="10" role="button" tabindex="0">Tee se itse & Projektit</span>
                <span class="badge badge-tag-11 tag-badge" data-tag="11" role="button" tabindex="0">Ympäristö & Luonto</span>
                <span class="badge badge-tag-12 tag-badge" data-tag="12" role="button" tabindex="0">Talous & Raha</span>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Julkaise</button>
    </form>

    <script>
        //Teeman vaihto ominaisuus
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            document.body.classList.add("dark-theme");
        }
        document.getElementById("themeToggle").addEventListener("click", () => {
            document.body.classList.toggle("dark-theme");
            if (document.body.classList.contains("dark-theme")) {
                localStorage.setItem("theme", "dark");
            } else {
                localStorage.setItem("theme", "light");
            }
        });

        // Tag badge -toiminnallisuus
        document.addEventListener('DOMContentLoaded', function() {
            const tagBadges = document.querySelectorAll('.tag-badge');
            
            tagBadges.forEach(badge => {
                // Click-käsittelijä
                badge.addEventListener('click', function() {
                    toggleTag(this);
                });
                
                // Näppäimistötuki
                badge.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleTag(this);
                    }
                });
            });
            
            function toggleTag(badge) {
                const tagId = badge.getAttribute('data-tag');
                const checkbox = document.getElementById('blogTag' + tagId);
                
                // Vaihda tila
                badge.classList.toggle('selected');
                checkbox.checked = badge.classList.contains('selected');
            }
        });
    </script>
    <script src="js/blogSubmit.js"></script>
</body>
</html>
