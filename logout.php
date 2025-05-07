<?php
session_start();
session_destroy();
header("Location: auth-cover-login.php");
exit();
?> 