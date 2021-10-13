<?php
function redirect_to_login_if_not_valid_teacher() {
    if (!isset($_SESSION["logged_teacher"]) || $_SESSION["logged_teacher"] != true) {
        header("Location: ../logout.php");
        exit();
    }
}