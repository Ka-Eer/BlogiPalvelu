
<!--
blogi.html ja send.php tulevat olla xampp > htdocs kansiossa, jolloin ne voi avata osoitteessa localhost/blogi.html --> 
<?php
$dbh = mysqli_connect('localhost', 'root', '', 'blogitekstit');
if (!$dbh) {
 die("Unable to connect to MySQL: " . mysqli_connect_error());
}
if (!mysqli_select_db($dbh, 'blogitekstit')) {
 die("Unable to select database: " . mysqli_error($dbh));
}
$otsikko = $_POST['blogTextTitle'];
$teksti = $_POST['blogText'];
$pvm = date('Y-m-d');
$kuva = NULL;
/* Jos kuva ei tyhjä, ottaa tiedoston nimen */
if (!empty($_FILES['blogImg']['name'])) {
    /* basename ottaa pois ylimääräiset kansiot; esim. C:\Users\Me\photo.jpg --> photo.jpg */
    $originalName = basename($_FILES['blogImg']['name']);
    /* Uniikki nimi käyttäen aikaa */
    $kuva = time() . "_" . $originalName;
}

$sql = "INSERT INTO blogit (Pvm, Otsikko, Teksti, Kuva) VALUES ('$pvm', '$otsikko', '$teksti', '$kuva')";
if (mysqli_query($dbh, $sql)) {
    echo "Toimii";
} else {
    echo "Error: " . mysqli_error($dbh);
}
?>