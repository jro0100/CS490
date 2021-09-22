<?php
session_start();

require("./util/functions.php");

function check_credentials($username, $password) {
    $sqlstmt = "SELECT * FROM users WHERE username = :username";
    $params = array(":username"=>$username);
    $result = db_execute($sqlstmt, $params);

    if ($result) {
        if (password_verify($password, $result["password"])) {
            if ($result["isAdmin"]) {
                $_SESSION["logged_admin"] = true;
            } else {
                $_SESSION["logged_user"] = true;
            }
            $_SESSION["username"] = $username;
            return true;
        }
    }
    return false;
}

// Checks if there is any reentrant data from the login form and validates it, then redirects accordingly
if (isset($_POST["username"]) && isset($_POST["password"])) {
    if (check_credentials($_POST["username"], $_POST["password"])) {
        if ((isset($_SESSION["logged_user"]) && $_SESSION["logged_user"] == true) || (isset($_SESSION["logged_admin"]) && $_SESSION["logged_admin"] == true)) {
            if (isset($_SESSION["logged_user"])) {
                $landingPage = "./user/";
            } elseif (isset($_SESSION["logged_admin"])) {
                $landingPage = "./admin/";
            } else {
                $landingPage = "index.php";
            }
            header("Location: " . $landingPage);
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--- Links to external CSS stylesheet --->
    <link rel="stylesheet"  href="css/login.css">
</head>
<body>
<!--- Displays login box in the center of the page --->
<div id="container">
    <div id="login">
        <div>
            <br>
            Sign In
        </div>
        <div id="error">
            Incorrect username or password
        </div>
        <form id="sign-in-form" method="post" action="index.php">
            <input type="text" id="username" name="username" placeholder="Username"><br><br>
            <input type="password" id="password" name="password" placeholder="Password"><br><br>
            <input type="submit" id="submit" value="Login"><br><br>
        </form>
    </div>
</div>

<!--- Javascript for basic logic --->
<script>
    /*
    const signInForm = document.getElementById("sign-in-form");
    const submitButton = document.getElementById("submit");
    const error = document.getElementById("error");

    submitButton.addEventListener("click", (e) => {
        e.preventDefault();
        const username = signInForm.username.value;
        const password = signInForm.password.value;

        if(username === "test" && password === "test") {
            alert("Logged In");
            location.reload();
        }
        else {
            error.style.opacity = 1;
            signInForm.username.value = "";
            signInForm.password.value = "";
        }
    })
     */
</script>
</body>
</html>
