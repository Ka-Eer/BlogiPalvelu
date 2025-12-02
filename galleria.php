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
    <title>Galleria - Blogipalvelu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="galleria.css">
</head>
<body id="Style">
    <header class="Otsikko1">
        <h1>Galleria</h1>
    </header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="Kuvat/Home.png" alt="Home" class="rounded-circle" width="48" height="48"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Etusivu</a></li>
                    <li class="nav-item"><a class="nav-link" href="luo_blogi.php">Luo blogi</a></li>
                    <li class="nav-item"><a class="nav-link active" href="galleria.php">Galleria</a></li>
                </ul>
                <!--Teeman vaihto nappi-->
                <button id="themeToggle" class="btn btn-outline-light ms-3">
                    Dark / Light
                </button>
                <!--Hakukenttä-->
                <form class="d-flex" role="search">
                    <input id="searchInput" class="form-control me-2" type="search" placeholder="Hae..." aria-label="Search">
                </form>
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
    
    <!-- pääsisältö -->
    <main class="container content-section">
        <div class="row g-4">
            <div class="col-12">
                <h2>Blogi-galleria</h2>
            </div>
        </div>
        <div class="row g-4">
            <!-- blogi lista -->
            <div class="col-12">
                <!-- Tagien valinta -->
                <div id="tag-filter-container" class="mb-3"></div>
                
                <!-- Blogikortit -->
                <div id="gallery" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <!-- Kortit täytetään JavaScript:llä -->
                </div>
                <div id="noResults" class="no-results" style="display:none;">Ei löytynyt blogeja.</div>
            </div>
        </div>
    </main>

    <!-- Bootstrap Modal blogipostaukselle (sama kuin etusivulla) -->
    <div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blogModalLabel">Blogipostaus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sulje"></button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" src="" alt="Kuva" class="img-fluid mb-3" style="width: 100%; max-height: 500px; object-fit: contain;">
                    <h4 id="modalTitle"></h4>
                    <hr>
                    <div id="modalTagit" class="mb-2"></div>
                    <p id="modalText" style="white-space: pre-wrap;"></p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small id="modalDate" class="text-muted"></small>
                        <span class="like-btn">
                            <button class="heart-button" id="modalHeartButton" aria-pressed="false" title="Tykkää" data-id="" aria-label="Tykkää">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </button>
                            <span class="like-count" id="modalLikeCount">0</span>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sulje</button>
                    <a id="modalOpenBlogLink" href="#" class="btn btn-primary">Avaa blogi</a>
                </div>
            </div>
        </div>
    </div>

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

        // Hakutoiminto
        document.getElementById("searchInput").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                const query = encodeURIComponent(this.value.trim());
                if (query.length > 0) {
                    window.location.href = "galleria.php?search=" + query;
                }
            }
        });

        // URL-parametrin haku
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        if (searchQuery) {
            document.getElementById('searchInput').value = decodeURIComponent(searchQuery);
        }

        // Modal-funktio
        function openBlogModal(post) {
            document.getElementById('modalTitle').textContent = post.Otsikko || '';
            document.getElementById('modalText').textContent = post.Teksti || '';
            document.getElementById('modalImage').src = post.Kuvasrc || 'Kuvat/Placeholder2.png';
            
            const modalTagit = document.getElementById('modalTagit');
            modalTagit.innerHTML = '';
            if (Array.isArray(post.Tagit) && post.Tagit.length > 0) {
                post.Tagit.forEach(tag => {
                    const badge = document.createElement('span');
                    badge.className = `badge badge-tag-${tag.tag_ID} me-1`;
                    badge.textContent = tag.tag_Nimi;
                    modalTagit.appendChild(badge);
                });
            }
            
            const timeOnly = post.Klo ? post.Klo.slice(0, 5) : '';
            document.getElementById('modalDate').textContent = post.Pvm + ' ' + timeOnly;
            
            const modalHeartBtn = document.getElementById('modalHeartButton');
            modalHeartBtn.setAttribute('data-id', String(post.ID));
            
            const cardBtn = document.querySelector(`.blog-card [data-id="${post.ID}"]`);
            const currentLiked = cardBtn ? cardBtn.classList.contains('liked') : (post.liked || false);
            
            if (currentLiked) {
                modalHeartBtn.classList.add('liked');
            } else {
                modalHeartBtn.classList.remove('liked');
            }
            modalHeartBtn.setAttribute('aria-pressed', currentLiked ? 'true' : 'false');
            modalHeartBtn.title = currentLiked ? 'Poista tykkäys' : 'Tykkää';
            
            const modalLikeCount = document.getElementById('modalLikeCount');
            const cardCountEl = document.querySelector(`[data-blog-id="${post.ID}"].like-count`);
            const currentCount = cardCountEl ? cardCountEl.textContent : String(post.Tykkaykset || 0);
            modalLikeCount.textContent = currentCount;
            modalLikeCount.setAttribute('data-blog-id', String(post.ID));
            
            document.getElementById('modalOpenBlogLink').href = 'blogi.php?id=' + encodeURIComponent(post.ID);
            
            const newModalHeartBtn = modalHeartBtn.cloneNode(true);
            modalHeartBtn.parentNode.replaceChild(newModalHeartBtn, modalHeartBtn);
            
            newModalHeartBtn.onclick = (e) => {
                e.preventDefault();
                if (typeof toggleLike === 'function') {
                    toggleLike(newModalHeartBtn);
                }
            };
            
            const modalElement = document.getElementById('blogModal');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
        }
    </script>
    <script src="js/galleryCards.js" defer></script>
</body>
</html>
