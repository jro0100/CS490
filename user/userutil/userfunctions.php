<?php
function redirect_to_login_if_not_user() {
    if (!isset($_SESSION["logged_user"]) || $_SESSION["logged_user"] != true) {
        header("Location: ../");
        exit();
    }
}