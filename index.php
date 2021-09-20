<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--- Links to external CSS stylesheet --->
    <link rel="stylesheet"  href="css/login.css">
</head>
<body>
    <!--- Displays login box in the center of the page--->
    <div id="container">
        <div id="login">
            <div>
                <br>
                Sign In
            </div>
            <div id="error">
                    Incorrect username or password
            </div>
            <form id="sign-in-form">
                <input type="text" id="username" name="username" placeholder="Username"><br><br>
                <input type="text" id="password" name="password" placeholder="Password"> <br><br>
                <input type="submit" id="submit" value="Login"><br><br>
            </form>
        </div>
    </div>

    <!--- Javascript for basic logic --->
    <script>
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
    </script>

    <?php
     // This PHP tag is only here so Heroku recognizes a language and does not fail to deploy.
     // When actual PHP is added, this can be taken out.
    ?>
</body>
</html>