<?php
function redirect_to_login_if_not_valid_student() {
    if (!isset($_SESSION["logged_student"]) || $_SESSION["logged_student"] != true) {
        header("Location: ../logout.php");
        exit();
    }
}