<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Hash</title>
</head>
<body>
    <div>
        <div id="hashed">
            <?php
            if (isset($_GET["plain"])) {
                echo $_GET["plain"] . "<br>";
                echo password_hash($_GET["plain"], PASSWORD_DEFAULT);
            }
            ?>
        </div>
        <form id="hashform" method="get" action="makehash.php">
            <input type="text" id="plain" name="plain" placeholder="Plaintext Here" required>
            <input type="submit">
        </form>
    </div>
</body>
</html>
