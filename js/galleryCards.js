// hakee kaikki blogipostaukset get_galleria.php:stä ja renderöi ne korteiksi

async function loadGalleryCards() {
    const container = document.getElementById('gallery');
    const noResults = document.getElementById('noResults');
    if (!container) return;

    try {
        const res = await fetch('get_galleria.php');
        if (!res.ok) throw new Error('Network response not ok');
        const posts = await res.json();

        container.innerHTML = '';
        if (!posts || posts.length === 0) {
            if (noResults) noResults.style.display = '';
            return;
        }

        if (noResults) noResults.style.display = 'none';

        posts.forEach(post => {
            const col = document.createElement('div');
            // Use the same column classes as the index's latestPosts.js so
            // gallery columns behave identically: 1 / 2 / 3 / 4 per row at
            // xs, sm, lg, xl breakpoints respectively.
            col.className = 'col-12 col-sm-6 col-lg-4 col-xl-3';

            const card = document.createElement('div');
            card.className = 'card gallery-card clickable';
            // make card keyboard-focusable
            card.tabIndex = 0;

            const img = document.createElement('img');
            img.src = post.Kuvasrc || 'Kuvat/Placeholder2.png';
            img.alt = 'Kuva';
            // Keep image natural sizing (like index) so card sizes match
            // latestPosts.js behaviour (no forced fixed thumbnail height).
            card.appendChild(img);

            const body = document.createElement('div');
            body.className = 'card-body';

            const title = document.createElement('h5');
            title.className = 'card-title';
            title.textContent = post.Otsikko || '';
            body.appendChild(title);

            const text = document.createElement('p');
            text.className = 'card-text';
            // lyhyt esikatselu (ensimmäiset 140 merkkiä)
            const preview = (post.Teksti || '').replace(/\s+/g, ' ').trim();
            text.textContent = preview.length > 140 ? preview.slice(0,140) + '…' : preview;
            body.appendChild(text);

            const read = document.createElement('a');
            read.className = 'btn btn-primary';
            // linkki tarkemmalle blogisivulle (blogi.html?id=ID)
            read.href = 'blogi.html?id=' + encodeURIComponent(post.ID);
            read.textContent = 'Lue lisää';
            body.appendChild(read);

            // päivämäärä ja aika
            const meta = document.createElement('div');
            meta.className = 'd-flex justify-content-between align-items-center mt-2';
            const date = document.createElement('small');
            date.className = 'text-muted';
            // näytä vain hh:mm (ilman sekunteja)
            const timeOnly = post.Klo ? post.Klo.slice(0, 5) : '';
            date.textContent = post.Pvm + ' ' + timeOnly;
            meta.appendChild(date);
            body.appendChild(meta);

            const likeWrap = document.createElement('span');
            likeWrap.className = 'like-btn';

            const btn = document.createElement('button');
            btn.className = 'heart-button';
            btn.setAttribute('aria-pressed', 'false');
            btn.setAttribute('title', 'Tykkää');
            btn.setAttribute('data-id', String(post.ID));
            btn.setAttribute('aria-label', 'Tykkää');

            // lisää sydänkuvake
            btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';

            const likeCount = document.createElement('span');
            likeCount.className = 'like-count';
            likeCount.id = 'likes-' + post.ID;
            likeCount.textContent = String(post.Tykkaykset || 0);

            likeWrap.appendChild(btn);
            likeWrap.appendChild(likeCount);
            
            // lisää tykkäykset meta-taulukkoon (päivämäärän kanssa)
            const likesSmall = document.createElement('small');
            likesSmall.className = 'text-muted';
            likesSmall.appendChild(likeWrap);
            
            // Etsi meta ja lisää like-span sinne
            const metaLikes = document.createElement('span');
            metaLikes.appendChild(btn);
            metaLikes.appendChild(likeCount);
            meta.appendChild(metaLikes);

            card.appendChild(body);
            col.appendChild(card);
            container.appendChild(col);

            // koko kortti klikattavaksi (navigoi samaan href:iin kuin 'Lue lisää')
            const cardNavigate = (event) => {
                // ignooraa klikkaukset, jotka tulevat sydännapista tai sen lapsielementeistä tai lue lisää -linkistä
                if (event.target.closest('.heart-button') || event.target.closest('a')) return;
                window.location.href = read.href;
            };
            card.addEventListener('click', cardNavigate);
            // näppäimmistö: Enter tai Space kun kortti on focussattuna
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    // jos fokus on interaktiivisessa lapsielementissä, ohita
                    const active = document.activeElement;
                    if (active && (active.classList && (active.classList.contains('heart-button') || active.tagName === 'A'))) return;
                    e.preventDefault();
                    window.location.href = read.href;
                }
            });
        });

        // lisää tykkäysnapin kuuntelijat
        attachLikeListeners();

    } catch (err) {
        console.error('Failed to load gallery:', err);
        if (noResults) noResults.style.display = '';
    }
}

function attachLikeListeners() {
    document.querySelectorAll('.heart-button').forEach(btn => {
        // välttää moninkertaiset event listenerit
        if (btn.__hasLikeHandler) return;
        btn.__hasLikeHandler = true;

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof toggleLike === 'function') {
                toggleLike(btn);
            }
        });

        btn.addEventListener('keydown', (e) => {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                if (typeof toggleLike === 'function') {
                    toggleLike(btn);
                }
            }
        });
    });
}

// Run when parsed; deferred script ensures DOM exists
document.addEventListener('DOMContentLoaded', () => {
    loadGalleryCards();
});
