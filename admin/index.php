<?php
session_start();
require("./adminutil/adminfunctions.php");

redirect_to_login_if_not_admin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h1 style="text-align:center;">
    Admin Landing Page
    </h1>
    <a href="../logout.php">Logout</a>
</body>
</html>