<?php
session_start();
session_destroy();
header('Location: questions_flash_dnb.php');
exit;
?>
