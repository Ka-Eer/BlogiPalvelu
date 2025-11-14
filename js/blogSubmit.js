document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('BlogForm');
    const msg = document.getElementById('postMessage');
    if (!form || !msg) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        msg.style.display = 'none';
        msg.textContent = '';
        // clear previous file error
        const fileError = document.getElementById('fileError');
        if (fileError) { fileError.style.display = 'none'; fileError.textContent = ''; }

        // client-side file size check (match server 15 MB limit)
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

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const fd = new FormData(form);
            const res = await fetch(form.action || 'blog_send.php', {
                method: 'POST',
                body: fd
            });

            const text = await res.text();

            // jos PHP palauttaa "Toimii" oletamme onnistuneen tallennuksen
            const success = res.ok && /toim(i|ii)/i.test(text);

            if (success) {
                msg.style.display = '';
                msg.style.background = '#d4edda';
                msg.style.color = '#155724';
                msg.style.padding = '10px 12px';
                msg.style.borderRadius = '6px';
                msg.textContent = 'Blogi postattu.';

                // Tyhjennä lomake kentät
                form.reset();

                // Poista mahdollinen paikallinen esikatselu tai päivitä näkymää tarvittaessa
            } else {
                msg.style.display = '';
                msg.style.background = '#f8d7da';
                msg.style.color = '#721c24';
                msg.style.padding = '10px 12px';
                msg.style.borderRadius = '6px';
                msg.textContent = 'Virhe lähetyksessä. Palvelimen vastaus: ' + text;
            }
        } catch (err) {
            console.error('Submit failed', err);
            msg.style.display = '';
            msg.style.background = '#f8d7da';
            msg.style.color = '#721c24';
            msg.style.padding = '10px 12px';
            msg.style.borderRadius = '6px';
            msg.textContent = 'Virhe: ' + (err.message || 'lähetys epäonnistui');
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});
