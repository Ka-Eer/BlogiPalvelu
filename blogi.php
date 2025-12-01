<?php
session_start();
$isLoggedIn = isset($_SESSION['user_ID']);
$username = $_SESSION['username'] ?? '';
?>
<!-- blogi.php
    sivulla näytetään yksittäinen blogipostaus id-parametrin perusteella
-->
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blogi - Postaus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="galleria.css">
</head>
<body id="Style">
    <!--Otsikko-->
    <header class="Otsikko1">
        <h1>Blogi</h1>
    </header>
    <!-- Navigaatio -->
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

    <!-- Pääsisältö -->
    <main class="container py-4" id="post-root">
        <div id="post" class="">
            <!-- Postaus renderöidään tänne -->
            <div id="loading" class="text-center py-5">Ladataan...</div>
        </div>
    </main>

<!-- Scriptit -->
<script>
// Teeman vaihto
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

// Hakee id param ja näyttää postauksen
(function() {
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    const params = new URLSearchParams(window.location.search);
    const blogId = params.get('id');

    if (!blogId) {
        document.getElementById('loading').innerHTML = '<div class="alert alert-warning">ID-parametri puuttuu.</div>';
        return;
    }

    fetch('PHP/get_post.php?id=' + encodeURIComponent(blogId))
        .then(r => r.json())
        .then(data => {
            const loading = document.getElementById('loading');
            if (!data || data.error) {
                loading.innerHTML = '<div class="alert alert-danger">Blogipostausta ei löytynyt.</div>';
                return;
            }

            const post = data;
            const timeOnly = post.Klo ? post.Klo.slice(0,5) : '';

            let tagsHtml = '';
            if (Array.isArray(post.Tagit) && post.Tagit.length > 0) {
                post.Tagit.forEach(tag => {
                    tagsHtml += `<span class="badge badge-tag-${tag.tag_ID} me-1">${escapeHtml(tag.tag_Nimi)}</span>`;
                });
            }

            const html = `
                <div class="card">
                    <img src="${escapeHtml(post.Kuvasrc) || 'Kuvat/Placeholder2.png'}" class="card-img-top" alt="Kuva" style="max-height:500px; object-fit:contain;">
                    <div class="card-body">
                        <h2 class="card-title">${escapeHtml(post.Otsikko)}</h2>
                        <div class="mb-2">${tagsHtml}</div>
                        <p class="card-text" style="white-space:pre-wrap;">${escapeHtml(post.Teksti)}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${escapeHtml(post.Pvm)} ${timeOnly}</small>
                            <span class="like-btn">
                                <button class="heart-button ${post.liked ? 'liked' : ''}" 
                                        data-id="${post.ID}" 
                                        aria-pressed="${post.liked ? 'true' : 'false'}"
                                        title="${post.liked ? 'Poista tykkäys' : 'Tykkää'}"
                                        aria-label="${post.liked ? 'Poista tykkäys' : 'Tykkää'}">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </button>
                                <span class="like-count" data-blog-id="${post.ID}">${post.Tykkaykset || 0}</span>
                            </span>
                        </div>
                    </div>
                </div>
            `;

            loading.innerHTML = html;

            const heartBtn = document.querySelector('.heart-button');
            if (heartBtn) {
                heartBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof toggleLike === 'function') {
                        toggleLike(this);
                    }
                });
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('loading').innerHTML = '<div class="alert alert-danger">Virhe ladattaessa blogipostausta.</div>';
        });
})();
</script>
<script src="js/blogScript.js"></script>
</body>
</html>
