<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/teacher/reviewExam.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Question Bank</a></li>
                <li class="nav-item"><a href="exams.php">Exams</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <form method="get" action="reviewExam.php">
            <div class="center">
                <select name="student" id="student">
                    <option>Test</option>
                    <option>Test2</option>
                </select><br>
                <input type="submit" class="submitButton" name="getStudent" value="Get Student"></input>
            <div>
        </form>

        <div class="row">
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Question
                </h1>
            </div>
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Answer
                </h1>
            </div>
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Points
                </h1>
            </div>
            <div class="columnHeader">
                <h1 class="center-column-text-font-size">
                    Comment
                </h1>
            </div>
        </div>

        <script>
            var text = <?php echo $json ?>;
            form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "reviewExam.php");
    
            for (i = 0; i < text.length; i++) {
    
                const obj = JSON.parse(JSON.stringify(text[i]));
    
                //Create row
                row = document.createElement("div");
                row.classList.add("row");
    
                //Create column
                questionCol = document.createElement("div");
                questionCol.classList.add("column");
                questionCol.classList.add("center-column-text");
                questionCol.innerHTML = obj.question;

                answerCol = document.createElement("div");
                answerCol.classList.add("column");
                answerCol.classList.add("center-column-text");
                answerCol.innerHTML = obj.answer;

                pointsCol = document.createElement("div");
                pointsCol.classList.add("columnHeader");
                points = document.createElement("input");
                points.classList.add("input-text-field");
                points.setAttribute("type", "input");
                points.setAttribute("name", "points-" + obj.questionID);
                points.value = obj.points;
                pointsCol.appendChild(points);

                commentCol = document.createElement("div");
                commentCol.classList.add("columnHeader");
                comment = document.createElement("input");
                comment.classList.add("input-text-field");
                comment.setAttribute("type", "input");
                comment.setAttribute("name", "comment-" + obj.questionID);
                if (obj.comment == "") {
                    comment.value = "No Comment";
                } else {
                    comment.value = obj.comment;
                }
                commentCol.appendChild(comment);
    
                row.appendChild(questionCol);
                row.appendChild(answerCol);
                row.appendChild(pointsCol);
                row.appendChild(commentCol);
                form.appendChild(row);
            }
            buttonDiv = document.createElement("div");
            buttonDiv.classList.add("center");
            createExamButton = document.createElement("input");
            createExamButton.setAttribute("type", "submit");
            createExamButton.setAttribute("class", "submitButton");
            createExamButton.setAttribute("name", "createExam");
            createExamButton.value = "Create Exam";
            buttonDiv.appendChild(createExamButton);
            form.appendChild(buttonDiv);
    
            document.body.appendChild(form); //Appends the div to the body of the HTML page
        </script>
    </body>
</html>