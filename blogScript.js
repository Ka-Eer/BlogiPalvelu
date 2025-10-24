function CreateBlog() {
    // Haetaan tiedot käyttäjän syöttämästä otsikosta, tekstistä ja kuvasta
    const New_Title = document.getElementById("blogTextTitle").value;
    const New_BlogText = document.getElementById("blogText").value;
    const New_BlogImg = document.getElementById("blogImg").files[0];

    // Varmistetaan, että otsikko ja teksti ovat olemassa
    if (New_Title && New_BlogText) {

        // Estää sivua latautumasta uudelleen nappia painaessa
        event.preventDefault()

        // Luodaan kortti blogille
        const Blog_Full = document.createElement("div");

        // Lisätään luokat ja tyylimuutokset luotavaan korttiin
        Blog_Full.classList.add("card");
        Blog_Full.style.marginTop = "1em";
        Blog_Full.style.width = "20em";
        Blog_Full.style.minWidth = "20em";
        Blog_Full.style.marginRight = "1em";
        Blog_Full.style.height = "25em";

        // Luodaan "Lue lisää" nappi
        const Blog_Button = document.createElement("button");

        // Luokat ja tyylimuutokset
        Blog_Button.classList.add("btn");
        Blog_Button.classList.add("btn-info");
        Blog_Button.textContent = "Lue lisää";
        // Varmistetaan, että nappi on kortin alareunassa
        Blog_Button.style.marginBottom = "0";
        // Tyylimuutokset
        Blog_Button.style.borderTopLeftRadius = "0";
        Blog_Button.style.borderTopRightRadius = "0";

        // Luodaan blogikortin tekstialue
        const Blog_TextArea = document.createElement("div");
        // Lisätään luokka
        Blog_TextArea.classList.add("card-body");

        // Luodaan otsikko korttiin
        const TitleElement = document.createElement("h2");

        // Jos otsikon pituus enemmän kuin 25 merkkiä:
        if (New_Title.length > 25) { 
            // Valitsee ensimmäiset 25 merkkiä ja lisää "..." loppuun
            TitleElement.textContent = New_Title.slice(0, 25) + "...";
        } else { // Muuten otsikko luodaan kokonaan
            // Otetaan otsikko käyttäjän syöttämästä otsikosta
            TitleElement.textContent = New_Title;
        }
        
        // Fonttikoko
        TitleElement.style.fontSize = "1.25em"

        // Jos kuva on liitetty:
        if (New_BlogImg) {
                // Luodaan blogiteksti
            const TextElement = document.createElement("p");
            // Lisätään käyttäjän syöttämä blogiteksti

            // Jos tekstin pituus enemmän kuin 125 merkkiä:
            if (New_BlogText.length > 125) {
                // Valitsee ensimmäiset 125 merkkiä ja lisää "..." loppuun
                TextElement.textContent = New_BlogText.slice(0, 125) + "...";
            } else { // Muuten teksti käyttäjän syötöstä
                TextElement.textContent = New_BlogText;
            } 
            // Fonttikoko
            TextElement.style.fontSize = "1em"

            // Filereader kuvan lukemista varten
            const reader = new FileReader();

            // Suoritetaan kun kuva on ladattu
            reader.onload = function(event) {

                // Luodaan kuva
                const ImgElement = document.createElement("img");
                // Otetaan kuvaksi käyttäjän syöttämä kuva
                ImgElement.src = event.target.result;

                // Kuva 40% kortin koosta
                ImgElement.style.height = "40%";
                // Kuva kroppaanttuu, eikä litisty
                ImgElement.style.objectFit = "cover";

                // Kuvan kulmat
                ImgElement.style.borderTopLeftRadius = "0.25em"
                ImgElement.style.borderTopRightRadius = "0.25em"

                // Lisätään otsikko ja blogiteksti kortin tekstialueeseen
                Blog_TextArea.appendChild(TitleElement);
                Blog_TextArea.appendChild(TextElement);

                // Lisätään kuva kortin yläosaan
                Blog_Full.appendChild(ImgElement);
                // Otsikko ja blogiteksti kuvan jälkeen
                Blog_Full.appendChild(Blog_TextArea);
                // Nappi kortin alareunaan
                Blog_Full.appendChild(Blog_Button)
            }

            // Lukee kuvan DataURL-muodossa, jotta se voidaan näyttää selaimessa
            reader.readAsDataURL(New_BlogImg);

        } else { // Jos kuvaa ei ole liitetty
            // Luodaan blogiteksti
            const TextElement = document.createElement("p");
            // Lisätään käyttäjän syöttämä blogiteksti

            // Jos tekstin pituus enemmän kuin 225 merkkiä:
            if (New_BlogText.length > 225) {
                // Valitsee ensimmäiset 225 merkkiä ja lisää "..." loppuun
                TextElement.textContent = New_BlogText.slice(0, 225) + "...";
            } else { // Muuten teksti käyttäjän syötöstä
                TextElement.textContent = New_BlogText;
            } 
            // Fonttikoko
            TextElement.style.fontSize = "1em"

            // Lisätään otsikko ja blogiteksti tekstialueeseen 
            Blog_TextArea.appendChild(TitleElement);
            Blog_TextArea.appendChild(TextElement);

            // Lisätään tekstialue ja nappi korttiin
            Blog_Full.appendChild(Blog_TextArea);
            Blog_Full.appendChild(Blog_Button)
        }
        
        // Haetaan div, johon blogi lisätään
        const Add_Blog_Here = document.getElementById("blogit");
        // Lisätään blogi
        Add_Blog_Here.appendChild(Blog_Full);

        // Tyhjentää täytettävät kohdat blogin lisäämisen jälkeen.
        document.getElementById("blogTextTitle").value = "";
        document.getElementById("blogText").value = "";
        document.getElementById("blogImg").value = "";
}}