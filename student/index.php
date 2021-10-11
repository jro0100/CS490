<?php
session_start();
require("./studentutil/student_functions.php");

redirect_to_login_if_not_valid_student();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/main.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.php">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </body>

    <script>
        var text = <?php echo $json ?>;

        for (i = 0; i < text.length; i++) {
            const obj = JSON.parse(JSON.stringify(text[i]));

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            aTag = document.createElement("a");
            aTag.setAttribute("href", "takeExam.php?examID=" + obj.examID);

            examName = document.createElement("p");
            examName.classList.add("center-column-text");
            examName.innerHTML = obj.examName;

            points = document.createElement("p");
            points.classList.add("center-column-text");
            points.innerHTML = "Points: " + obj.totalPoints;

            aTag.appendChild(examName);
            aTag.appendChild(points);
            column.appendChild(aTag);
            row.appendChild(column);

            document.body.appendChild(row);
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "YOU HAVE NO OUTSTANDING EXAMS TO BE TAKEN!";
            document.body.appendChild(emptiness);
        }
    </script>
</html>