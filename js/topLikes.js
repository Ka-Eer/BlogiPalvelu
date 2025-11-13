// topLikes.js
// Hakee get_top_likes.php:stä top 10 tykkäysten mukaan ja renderöi ne

document.addEventListener('DOMContentLoaded', () => {
    const listEl = document.getElementById('top-list');
    if (!listEl) return;

    fetch('get_top_likes.php')
        .then(resp => {
            if (!resp.ok) throw new Error('Network response was not ok');
            return resp.json();
        })
        .then(data => {
            // Jos palautettiin virhe
            if (data && data.error) {
                listEl.innerHTML = `<li>Virhe: ${escapeHtml(data.error)}</li>`;
                return;
            }

            if (!Array.isArray(data) || data.length === 0) {
                listEl.innerHTML = '<li>Ei suosittuja blogeja.</li>';
                return;
            }

            // Paginaatio: näytetään aluksi pageSize määrää ja lisätään "Lisää" -painike, jos enemmän
            const all = data;
            const pageSize = 5;
            let current = 0;

            listEl.innerHTML = '';

            function renderItem(item) {
                const id = item.ID || item.id || '';
                const title = item.Otsikko || item.otsikko || 'Ilman otsikkoa';
                const likes = typeof item.Tykkaykset === 'number' ? item.Tykkaykset : (parseInt(item.Tykkaykset) || 0);

                const li = document.createElement('li');
                li.className = 'top-item mb-2 clickable';
                // lista itemi näppäin fokusoitavaksi
                li.tabIndex = 0;

                const a = document.createElement('a');
                a.href = `blogi.html?id=${encodeURIComponent(id)}`;
                a.textContent = title;

                const heart = document.createElement('span');
                heart.className = 'top-heart ms-2 d-inline-flex align-items-center';
                heart.innerHTML = `
                    <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span class="like-num ms-1">${likes}</span>
                `;

                li.appendChild(a);
                li.appendChild(heart);
                listEl.appendChild(li);

                // tekee koko listaelementistä klikattavan (navigoi samaan href:iin kuin anchor)
                li.addEventListener('click', (event) => {
                    // ignooraa klikkaukset sydämestä tai sisemmistä ankkureista
                    if (event.target.closest('.top-heart') || event.target.closest('a')) return;
                    window.location.href = a.href;
                });
                li.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        const active = document.activeElement;
                        if (active && (active.classList && active.classList.contains('top-heart'))) return;
                        e.preventDefault();
                        window.location.href = a.href;
                    }
                });
            }

            // Lataa seuraavat pageSize elementtiä
            function renderNext() {
                const chunk = all.slice(current, current + pageSize);
                chunk.forEach(renderItem);
                current += chunk.length;
                // jos kaikki näytetty, poista nappi
                if (current >= all.length && loadMoreBtn && loadMoreBtn.parentNode) {
                    loadMoreBtn.remove();
                }
            }

            // luo "Lisää" ja "Vähemmän" painikkeet
            const loadMoreBtn = document.createElement('button');
            loadMoreBtn.type = 'button';
            loadMoreBtn.className = 'btn btn-link p-0 mt-2';
            loadMoreBtn.textContent = 'Lisää';

            const showLessBtn = document.createElement('button');
            showLessBtn.type = 'button';
            showLessBtn.className = 'btn btn-link p-0 mt-2';
            showLessBtn.textContent = 'Vähemmän';

            loadMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                renderNext();
                // jos kaikki nyt näytetty, vaihdetaan nappi "Vähemmän"
                if (current >= all.length) {
                    // poista Lisää ja lisää Vähemmän jos ei jo lisätty
                    if (loadMoreBtn.parentNode) loadMoreBtn.remove();
                    if (!showLessBtn.parentNode) listEl.insertAdjacentElement('afterend', showLessBtn);
                }
            });

            showLessBtn.addEventListener('click', (e) => {
                e.preventDefault();
                // palauta takaisin ensimmäiseen sivuun
                current = 0;
                // Poista kaikki li-elementit ja renderöi ensimmäiset
                listEl.innerHTML = '';
                renderNext();
                // Vaihda napit: poista Vähemmän ja lisää Lisää jos tarpeen
                if (showLessBtn.parentNode) showLessBtn.remove();
                if (all.length > pageSize && !loadMoreBtn.parentNode) listEl.insertAdjacentElement('afterend', loadMoreBtn);
            });

            // renderöidään aluksi ensimmäinen sivu
            renderNext();

            if (all.length > pageSize) {
                // Lisätään Lisää-nappi listan jälkeen
                listEl.insertAdjacentElement('afterend', loadMoreBtn);
            }
        })
        // jos ei saada yhteyttä palvelimeen
        .catch(err => {
            console.error('Failed to load top likes:', err);
            listEl.innerHTML = `<li>Ei saatu yhteyttä palvelimeen.</li>`;
        });
});

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
