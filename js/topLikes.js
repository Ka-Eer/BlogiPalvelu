// topLikes.js
// Hakee get_top_likes.php:stä top 10 tykkäysten mukaan ja renderöi ne

async function loadTopLikes() {
    const listEl = document.getElementById('top-list');
    if (!listEl) return; // jos ei löydy, lopeta

    // hakee tiedot palvelimelta
    try {
    const resp = await fetch('PHP/get_top_likes.php'); // kutsuu get_top_likes.php
        if (!resp.ok) throw new Error('Network response was not ok'); //tarkistaa että vastaus on ok
        const data = await resp.json();

        // Jos palautettiin virhe
        if (data && data.error) {
            listEl.innerHTML = `<li>Virhe: ${escapeHtml(data.error)}</li>`;
            return;
        }

        // Jos data ei ole taulukko tai on tyhjä
        if (!Array.isArray(data) || data.length === 0) {
            listEl.innerHTML = '<li>Ei suosittuja blogeja.</li>';
            return;
        }

        // Paginaatio: näytetään aluksi pageSize määrää ja lisätään "Lisää" -painike, jos enemmän kuin 5
        const all = data;
        const pageSize = 5;
        let current = 0;

        // Tyhjennä lista ennen täyttöä
        listEl.innerHTML = '';

        // renderöi item
        function renderItem(item) {
            const id = item.blog_ID || item.ID || item.id || '';
            const title = item.Otsikko || item.otsikko || 'Ilman otsikkoa';
            const likes = typeof item.Tykkaykset === 'number' ? item.Tykkaykset : (parseInt(item.Tykkaykset) || 0);

            // luo listan
            const li = document.createElement('li');
            li.className = 'top-item mb-2 clickable'; // lista itemin luokat
            // lista itemi näppäin fokusoitavaksi
            li.tabIndex = 0;

            // Otsikko linkkinä
            const a = document.createElement('a');
            // linkki tarkemmalle blogisivulle (blogi.html?id=ID)
            a.href = `blogi.html?id=${encodeURIComponent(id)}`;
            a.textContent = title;

            // sydän ja tykkäyslaskuri
            const heart = document.createElement('span');
            heart.className = 'top-heart ms-2 d-inline-flex align-items-center';
            heart.innerHTML = `
                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span class="like-num ms-1">${likes}</span>
            `;

            // lisää otsikkolinkki ja sydän listaelementtiin
            li.appendChild(a);
            li.appendChild(heart);
            listEl.appendChild(li);

            // tekee koko listaelementistä klikattavan (navigoi samaan href:iin kuin otsikko)
            li.addEventListener('click', (event) => {
                // ignooraa klikkaukset sydämestä tai sisemmistä linkeistä
                if (event.target.closest('.top-heart') || event.target.closest('a')) return;
                window.location.href = a.href;
            });
            // tekee koko listaelementistä näppäin-navigoitavan
            li.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    const active = document.activeElement;
                    if (active && (active.classList && active.classList.contains('top-heart'))) return;
                    e.preventDefault();
                    window.location.href = a.href;
                }
            });
        }

        // Lataa seuraavat pageSize(5) elementtiä
        function renderNext() {
            const chunk = all.slice(current, current + pageSize);
            chunk.forEach(renderItem);
            current += chunk.length;
            // jos kaikki näytetty, poista lisää nappi
            if (current >= all.length && loadMoreBtn && loadMoreBtn.parentNode) {
                loadMoreBtn.remove();
            }
        }

        // "Lisää" nappi
        const loadMoreBtn = document.createElement('button');
        loadMoreBtn.type = 'button';
        loadMoreBtn.className = 'btn btn-link p-0 mt-2';
        loadMoreBtn.textContent = 'Lisää';

        // "Vähemmän" nappi
        const showLessBtn = document.createElement('button');
        showLessBtn.type = 'button';
        showLessBtn.className = 'btn btn-link p-0 mt-2';
        showLessBtn.textContent = 'Vähemmän';

        // "lisää" napille kuuntelija
        loadMoreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            renderNext();
            //jos kaikki näytetty, vaihdetaan nappi "Vähemmän"
            if (current >= all.length) { 
                if (loadMoreBtn.parentNode) loadMoreBtn.remove();
                if (!showLessBtn.parentNode) listEl.insertAdjacentElement('afterend', showLessBtn);
            }
        });

        // "Vähemmän" napille kuuntelija
        showLessBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // palauta takaisin ensimmäiseen sivuun
            current = 0;
            // Poista kaikki li-elementit ja renderöi ensimmäiset
            listEl.innerHTML = '';
            renderNext();
            // poista "Vähemmän" nappi jos se on näkyvissä
            if (showLessBtn.parentNode) showLessBtn.remove();
            if (all.length > pageSize && !loadMoreBtn.parentNode) listEl.insertAdjacentElement('afterend', loadMoreBtn);
        });

        // renderöidään aluksi ensimmäinen sivu
        renderNext();

        // jos on enemmän kuin pageSize(5), näytetään "Lisää" nappi
        if (all.length > pageSize) {
            // Lisätään Lisää-nappi listan jälkeen
            listEl.insertAdjacentElement('afterend', loadMoreBtn);
        }

    } catch (err) {// jos fetch epäonnistuu
        console.error('Failed to load top likes:', err);
        listEl.innerHTML = `<li>Ei saatu yhteyttä palvelimeen.</li>`;
    }
}

// Run when parsed; deferred script ensures DOM exists
document.addEventListener('DOMContentLoaded', () => {
    loadTopLikes();
});