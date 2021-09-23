<?php
session_start();
require("./userutil/userfunctions.php");

redirect_to_login_if_not_valid_user();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h1 style="text-align:center;">
    User Landing Page
    </h1>
    <a href="../logout.php" style="text-align:center;">Logout</a>
</body>
</html>