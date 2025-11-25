// js/blogSubmit.js
// Lomakkeen lähetys blogipostauksen tekemiseen

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('BlogForm');
    const msg = document.getElementById('postMessage');
    if (!form || !msg) return;

    // lomakkeen lähetys tapahtuma
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        msg.style.display = 'none';
        msg.textContent = '';
        // tyhjennä aiempi tiedostovirhe
        const fileError = document.getElementById('fileError');
        if (fileError) { fileError.style.display = 'none'; fileError.textContent = ''; }

        // tarkista tiedoston koko client-puolella (vastaamaan palvelimen 15 MB rajaa)
        const fileInput = form.querySelector('#blogImg');
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            const f = fileInput.files[0];
            const maxBytes = 15 * 1024 * 1024; // 15 MB
            if (f.size > maxBytes) {
                if (fileError) {
                    fileError.textContent = 'Kuva on liian suuri. Maksimikoko on 15 MB.';
                    fileError.style.display = '';
                }
                if (submitBtn) submitBtn.disabled = false;
                return;
            }
        }

        // Poista lähetysnappi käytöstä estämään kaksoislähetykset
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        // Lähetä lomake palvelimelle
        try {
            const fd = new FormData(form);
            const res = await fetch(form.action || 'blog_send.php', { //blog_send.php
                method: 'POST',
                body: fd
            });

            // luetaan palvelimen vastaus tekstinä
            const text = await res.text();

            // jos PHP palauttaa "Toimii" oletamme onnistuneen tallennuksen
            const success = res.ok && /toim(i|ii)/i.test(text);

            // Näytä viesti käyttäjälle
            if (success) { // onnistui
                msg.style.display = '';
                msg.style.background = '#d4edda';
                msg.style.color = '#155724';
                msg.style.padding = '10px 12px';
                msg.style.borderRadius = '6px';
                msg.textContent = 'Blogi postattu.';

                // Tyhjennä lomake kentät
                form.reset();
            } else {// epäonnistui
                msg.style.display = '';
                msg.style.background = '#f8d7da';
                msg.style.color = '#721c24';
                msg.style.padding = '10px 12px';
                msg.style.borderRadius = '6px';
                // virheviesti palvelimelta
                msg.textContent = 'Virhe lähetyksessä. Palvelimen vastaus: ' + text;
            }
        } catch (err) { // verkko- tai muu virhe
            console.error('Submit failed', err);
            msg.style.display = '';
            msg.style.background = '#f8d7da';
            msg.style.color = '#721c24';
            msg.style.padding = '10px 12px';
            msg.style.borderRadius = '6px';
            msg.textContent = 'Virhe: ' + (err.message || 'lähetys epäonnistui');
        } finally { // ota lähetysnappi takaisin käyttöön
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});
