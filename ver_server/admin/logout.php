<?php
session_start();

unset($_SESSION['login_man']);
session_destroy();

echo "<meta http-equiv='refresh' content=\"0;URL=/admin/\">";
?>