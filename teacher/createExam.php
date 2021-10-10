<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <link rel="stylesheet"  href="../css/main.css">
    <link rel="stylesheet"  href="../css/teacher/index.css">
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-item"><a href="index.php">Question Bank</a></li>
            <li class="nav-item"><a href="exams.php">Exams</a></li>
            <li class="nav-item"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <script>
        var text = <?php echo $json ?>;

        for (i = 0; i < text.length; i++) {

            const obj = JSON.parse(JSON.stringify(text[i]));

            difficulty = "";
            if(obj.difficulty == 0) {
                difficulty = "Easy";
            } else if (obj.difficulty == 1) {
                difficulty = "Medium";
            } else {
                difficulty = "Hard";
            }

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            question = document.createElement("p");
            question.classList.add("center-column-text");
            question.innerHTML = obj.question;

            typeAndDif = document.createElement("p");
            typeAndDif.classList.add("center-column-text");
            typeAndDif.innerHTML = "Type: " + obj.questionType + "&emsp;" + "Difficulty: " + difficulty;

            form = document.createElement("form");
            form.classList.add("center-column-text");
            form.setAttribute("method", "post");
            form.setAttribute("action", "exam.php");
            
            input = document.createElement("input");
            input.setAttribute("type", "text");
            input.setAttribute("id", "pointValue");
            input.setAttribute("placeholder", "Point Value");

            column.appendChild(question);
            column.appendChild(typeAndDif);
            form.appendChild(input);
            column.appendChild(form);
            row.appendChild(column);

            document.body.appendChild(row); //Appends the div to the body of the HTML page
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "NO QUESTIONS EXIST YET!";
            document.body.appendChild(emptiness);
        }
    </script>
    
    <div style="text-align:center" style="padding-top:10px">
        <form action="question.php">
            <input type="submit" class="submitButton" value="Save Exam"/>
        </form>
    </div>

</body>
</html>