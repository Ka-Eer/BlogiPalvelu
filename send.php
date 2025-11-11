<!-- Ei toimi vielä
<?php
$dbh = mysqli_connect('localhost', 'root', '');
if (!$dbh) {
 die("Unable to connect to MySQL: " . mysqli_connect_error());
}
if (!mysqli_select_db($dbh, 'blogitekstit')) {
 die("Unable to select database: " . mysqli_error($dbh));
}
$otsikko = $_POST['blogTextTitle'];
$teksti = $_POST['blogText'];
$pvm = date('Y-m-d');
$sqlCheck = mysqli_query($dbh, "SELECT * FROM blogit");
$sql = "INSERT INTO blogit (`Pvm`, `Otsikko`, `Teksti`) VALUES ('$pvm', '$otsikko', '$teksti')";
if (mysqli_query($dbh, $sql)) {
    echo "Toimii";
} else {
    echo "Error: " . mysqli_error($dbh);
}
?>