// latestPosts.js
// hakee uusimmat blogipostaukset get_latest.php:stä ja renderöi ne korteiksi

async function loadLatestPosts() { // lataa uusimmat blogipostaukset
    const container = document.getElementById('latest-row');
    const noResults = document.getElementById('noResults');
    if (!container) return; // jos ei löydy, lopeta

    // hae blogipostaukset palvelimelta
    try {
    const res = await fetch('PHP/get_latest.php');// kutsuu get_latest.php
        if (!res.ok) throw new Error('Network response not ok'); // tarkistaa että vastaus on ok
        const posts = await res.json();

        // tyhjennä vanhat tulokset
        container.innerHTML = '';
        if (!posts || posts.length === 0) {
            noResults.style.display = '';
            return;
        }
        
        // piilota "ei tuloksia" -viesti
        noResults.style.display = 'none';


        // Korttien renderöinti

        posts.forEach(post => {
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-lg-4 col-xl-3';
            const card = document.createElement('div');
            card.className = 'card blog-card clickable';
            card.tabIndex = 0;
            const img = document.createElement('img');
            img.src = post.Kuvasrc || 'Kuvat/Placeholder2.png';
            img.alt = 'Kuva';
            card.appendChild(img);
            const body = document.createElement('div');
            body.className = 'card-body';
            const title = document.createElement('h5');
            title.className = 'card-title';
            title.textContent = post.Otsikko || '';
            body.appendChild(title);
            const hr = document.createElement('hr');
            body.appendChild(hr);
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
            const text = document.createElement('p');
            text.className = 'card-text';
            const preview = (post.Teksti || '').replace(/\s+/g, ' ').trim();
            text.textContent = preview.length > 140 ? preview.slice(0,140) + '…' : preview;
            body.appendChild(text);
            const read = document.createElement('a');
            read.className = 'btn btn-primary';
            read.href = 'blogi.php?id=' + encodeURIComponent(post.blog_ID || post.ID);
            read.textContent = 'Lue lisää';
            body.appendChild(read);
            const meta = document.createElement('div');
            meta.className = 'd-flex justify-content-between align-items-center mt-2';
            const date = document.createElement('small');
            date.className = 'text-muted';
            const timeOnly = post.Klo ? post.Klo.slice(0, 5) : '';
            date.textContent = post.Pvm + ' ' + timeOnly;
            meta.appendChild(date);
            body.appendChild(meta);
            // Tykkäysnappi ja laskuri
            const btn = document.createElement('button');
            btn.className = 'heart-button';
            btn.setAttribute('aria-pressed', post.liked ? 'true' : 'false');
            btn.setAttribute('title', post.liked ? 'Poista tykkäys' : 'Tykkää');
            btn.setAttribute('data-id', String(post.blog_ID || post.ID));
            btn.setAttribute('aria-label', 'Tykkää');
            if (post.liked) btn.classList.add('liked');
            btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3 c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
            const likeCount = document.createElement('span');
            likeCount.className = 'like-count';
            likeCount.setAttribute('data-blog-id', String(post.blog_ID || post.ID));
            likeCount.id = 'likes-' + (post.blog_ID || post.ID);
            likeCount.textContent = String(post.Tykkaykset || 0);
            // Like-napin event
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleLike(btn);
            });
            btn.addEventListener('keydown', function(e) {
                if (e.key === ' ' || e.key === 'Enter') {
                    e.preventDefault();
                    toggleLike(btn);
                }
            });
            // Like-nappi ja laskuri samaan spaniin
            const metaLikes = document.createElement('span');
            metaLikes.appendChild(btn);
            metaLikes.appendChild(likeCount);
            meta.appendChild(metaLikes);
            card.appendChild(body);
            col.appendChild(card);
            container.appendChild(col);
            // Kortin klikkaus avaa modaalin
            const cardNavigate = (event) => {
                if (event.target.closest('.heart-button') || event.target.closest('a')) return;
                if (typeof openBlogModal === 'function') {
                    openBlogModal(post);
                }
            };
            card.addEventListener('click', cardNavigate);
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    const active = document.activeElement;
                    if (active && (active.classList && (active.classList.contains('heart-button') || active.tagName === 'A'))) return;
                    e.preventDefault();
                    if (typeof openBlogModal === 'function') {
                        openBlogModal(post);
                    }
                }
            });
        });

    } catch (err) {
        console.error('Failed to load latest posts', err);
        if (noResults) noResults.style.display = '';
    }
}

// AJAX-pohjainen toggleLike-funktio
function toggleLike(button) {
    const blogId = button.getAttribute('data-id');
    fetch('PHP/like_toggle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'blog_ID=' + encodeURIComponent(blogId)
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        // Päivitä KAIKKI tämän blogin tykkäysnapit (kortit + modaali)
        document.querySelectorAll(`[data-id="${blogId}"]`).forEach(btn => {
            btn.classList.toggle('liked', data.liked);
            btn.setAttribute('aria-pressed', data.liked ? 'true' : 'false');
            btn.title = data.liked ? 'Poista tykkäys' : 'Tykkää';
        });
        // Päivitä KAIKKI tämän blogin tykkäyslaskurit (sekä kortissa että modaalissa)
        document.querySelectorAll(`[data-blog-id="${blogId}"].like-count`).forEach(countEl => {
            countEl.textContent = data.count;
        });
    })
    .catch(err => {
        console.error('Like toggle failed:', err);
    });
}

// Run when parsed; deferred script ensures DOM exists
document.addEventListener('DOMContentLoaded', () => {
    loadLatestPosts();
});
