<?php
session_start();

// Tuhotaan sessio
session_unset();
session_destroy();

// Ohjataan takaisin etusivulle
header('Location: ../index.php');
exit();
?>
