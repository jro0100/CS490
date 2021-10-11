<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/main.css">
        <link rel="stylesheet"  href="../css/student/exams.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.html">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </body>

    <script>
        var text = <?php echo $json ?>;

        for (i = 0; i < text.length; i++) {
            const obj = JSON.parse(JSON.stringify(text[i]));

            form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "index.php");

            question = document.createElement("p");
            question.classList.add("center-column-text");
            question.innerHTML = (i + 1) + ".) " + obj.question;

            points = document.createElement("p");
            points.classList.add("center-column-text");
            points.innerHTML = "(Points: " + obj.points;

            textArea = document.createElement("textarea");
            textArea.setAttribute("name", obj.questionID);

            form.appendChild(question);
            form.appendChild(points);
            form.appendChild(textArea);
        }

        submitExam = document.createElement("input");
        submitExam.setAttribute("type", "submit");
        submitExam.setAttribute("name", "submitExam");
        submitExam.classList.add("submitButton");

        form.appendChild(submitExam);
        document.body.appendChild(form);
    </script>
</html>