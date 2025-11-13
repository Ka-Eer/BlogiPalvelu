// hakee kaikki blogipostaukset get_galleria.php:stä ja renderöi ne kortteiksi

async function loadGalleryCards() {
    const gallery = document.getElementById('gallery');
    const noResults = document.getElementById('noResults');
    if (!gallery) return;

    try {
        const res = await fetch('get_galleria.php');
        if (!res.ok) throw new Error('Network response not ok');
        const rows = await res.json();

        gallery.innerHTML = '';
        if (!rows || rows.length === 0) {
            noResults.style.display = 'block';
            return;
        }

        noResults.style.display = 'none';

        rows.forEach(r => {
            const col = document.createElement('div');
            col.className = 'card gallery-item gallery-card';

            // Kuva: jos Kuva puuttuu, näytä placeholder
            const img = document.createElement('img');
            img.className = 'card-img-top';
            img.alt = r.Otsikko || 'Blogi-kuva';
            img.src = r.Kuva ? ('data:image/*;base64,' + r.Kuva) : 'Kuvat/Placeholder2.png';

            const body = document.createElement('div');
            body.className = 'card-body';

            const title = document.createElement('h5');
            title.className = 'card-title';
            title.textContent = r.Otsikko;

            const p = document.createElement('p');
            p.className = 'card-text';
            p.textContent = r.Teksti.length > 120 ? r.Teksti.slice(0, 120) + '...' : r.Teksti;

            // lue lisää -linkki
            const read = document.createElement('a');
            read.className = 'btn btn-primary';
            read.href = 'blogi.html?id=' + encodeURIComponent(r.ID);
            read.textContent = 'Lue lisää';

            // päivämäärä ja aika
            const meta = document.createElement('div');
            meta.className = 'd-flex justify-content-between align-items-center mt-2';
            
            const date = document.createElement('small');
            date.className = 'text-muted';
            // näytä vain hh:mm (ilman sekunteja)
            const timeOnly = r.Klo ? r.Klo.slice(0, 5) : '';
            date.textContent = r.Pvm + ' ' + timeOnly;
            meta.appendChild(date);

            // tykkäysnappi ja laskuri
            const likeWrap = document.createElement('span');
            likeWrap.className = 'like-btn d-flex align-items-center';

            const btn = document.createElement('button');
            btn.className = 'heart-button';
            btn.setAttribute('aria-pressed', 'false');
            btn.setAttribute('title', 'Tykkää');
            btn.setAttribute('data-id', String(r.ID));
            btn.setAttribute('aria-label', 'Tykkää');
            btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';

            const likeCount = document.createElement('span');
            likeCount.className = 'like-count';
            likeCount.id = 'likes-' + r.ID;
            likeCount.textContent = String(r.Tykkaykset || 0);

            likeWrap.appendChild(btn);
            likeWrap.appendChild(likeCount);
            meta.appendChild(likeWrap);

            body.appendChild(title);
            body.appendChild(p);
            body.appendChild(read);
            body.appendChild(meta);

            col.appendChild(img);
            col.appendChild(body);

            gallery.appendChild(col);
        });

        // lisää tykkäysnapin kuuntelijat
        attachLikeListeners();

    } catch (err) {
        console.error('Failed to load gallery:', err);
        noResults.textContent = 'Ei saatu yhteyttä palvelimeen.';
        noResults.style.display = 'block';
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
