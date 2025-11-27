// galleryCards.js
// hakee kaikki blogipostaukset get_galleria.php:stä ja renderöi ne korteiksi

async function loadGalleryCards() { // lataa gallerian blogipostaukset
    const container = document.getElementById('gallery');
    const noResults = document.getElementById('noResults');
    if (!container) return; // jos ei löydy, lopeta

    // hakee tiedot palvelimelta
    try {
        const res = await fetch('get_galleria.php');//kutsuu get_galleria.php
        if (!res.ok) throw new Error('Network response not ok');// tarkistaa että vastaus on ok
        const posts = await res.json();

        // tyhjennä vanhat tulokset
        container.innerHTML = '';
        if (!posts || posts.length === 0) {
            if (noResults) noResults.style.display = '';
            return;
        }

        // piilota "ei tuloksia" -viesti
        if (noResults) noResults.style.display = 'none';


        // Korttien renderöinti
        posts.forEach(post => { // käydään jokainen postaus läpi
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-lg-4 col-xl-3';//BS sarake luokat

            //luo kortti
            const card = document.createElement('div');
            card.className = 'card gallery-card clickable';
            card.tabIndex = 0;

            // Luo kuva elementti
            const img = document.createElement('img');
            img.src = post.Kuvasrc || 'Kuvat/Placeholder2.png';
            img.alt = 'Kuva';
            card.appendChild(img);

            //luo kortin runko
            const body = document.createElement('div');
            body.className = 'card-body';

            //otsikko
            const title = document.createElement('h5');
            title.className = 'card-title';
            title.textContent = post.Otsikko || '';
            body.appendChild(title);

            // erotinviiva
            const hr = document.createElement('hr');
            body.appendChild(hr);

            // Tagit Bootstrap badgeina
            if (Array.isArray(post.Tagit) && post.Tagit.length > 0) {
                const tagWrap = document.createElement('div');
                tagWrap.className = 'mb-2';
                post.Tagit.forEach(tag => {
                    const badge = document.createElement('span');
                    badge.className = `badge badge-tag-${tag.tag_ID} me-1`;
                    badge.textContent = tag.tag_Nimi;
                    badge.setAttribute('data-tag-id', tag.tag_ID);
                    tagWrap.appendChild(badge);
                });
                body.appendChild(tagWrap);
            }

            //esikatselu teksti
            const text = document.createElement('p');
            text.className = 'card-text';
            const preview = (post.Teksti || '').replace(/\s+/g, ' ').trim();
            //esikatselu (max 140 merkkiä)
            text.textContent = preview.length > 140 ? preview.slice(0,140) + '…' : preview;
            body.appendChild(text);

            // Lue lisää linkki
            const read = document.createElement('a');
            read.className = 'btn btn-primary';
            // linkki tarkemmalle blogisivulle (blogit.html?id=ID)
            read.href = 'blogit.html?id=' + encodeURIComponent(post.blog_ID || post.ID);
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

            //tykkäykset
            const likeWrap = document.createElement('span');
            likeWrap.className = 'like-btn';
            // Luo tykkäysnappi
            const btn = document.createElement('button');
            btn.className = 'heart-button';
            btn.setAttribute('aria-pressed', 'false');
            btn.setAttribute('title', 'Tykkää');
            btn.setAttribute('data-id', String(post.blog_ID || post.ID));
            btn.setAttribute('aria-label', 'Tykkää');

            // lisää sydänkuvake
            btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';

            // tykkäyslaskuri
            const likeCount = document.createElement('span');
            likeCount.className = 'like-count';
            likeCount.id = 'likes-' + (post.blog_ID || post.ID);
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

            // koko kortin rakentaminen
            card.appendChild(body);
            col.appendChild(card);
            container.appendChild(col);

            // koko kortti klikattavaksi (avaa modalin)
            const cardNavigate = (event) => {
                // ignooraa klikkaukset, jotka tulevat sydännapista tai sen lapsielementeistä tai lue lisää -linkistä
                if (event.target.closest('.heart-button') || event.target.closest('a')) return;
                // Avaa modal täydellä blogitekstillä
                if (typeof openBlogModal === 'function') {
                    openBlogModal(post);
                }
            };
            card.addEventListener('click', cardNavigate);
            // näppäimmistö: Enter tai Space kun kortti on focussattuna
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    // jos fokus on interaktiivisessa lapsielementissä, ohita
                    const active = document.activeElement;
                    if (active && (active.classList && (active.classList.contains('heart-button') || active.tagName === 'A'))) return;
                    e.preventDefault();
                    // Avaa modal täydellä blogitekstillä
                    if (typeof openBlogModal === 'function') {
                        openBlogModal(post);
                    }
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

// tykkäysnapin event listenerit
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
