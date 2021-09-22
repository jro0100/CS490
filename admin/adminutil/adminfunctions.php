<?php
function redirect_to_login_if_not_valid_admin() {
    if (!isset($_SESSION["logged_admin"]) || $_SESSION["logged_admin"] != true) {
        header("Location: ../");
        exit();
    }
}