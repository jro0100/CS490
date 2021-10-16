<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet"  href="../css/menu.css">
        <link rel="stylesheet"  href="../css/reviewExam.css">
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-item"><a href="index.php">Oustanding Exams</a></li>
                <li class="nav-item"><a href="grades.php">Grades</a></li>
                <li class="nav-item"><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

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

                //Create column
                answerCol = document.createElement("div");
                answerCol.classList.add("column");
                answerCol.classList.add("center-column-text");
                answerCol.innerHTML = obj.answer;

                //Create column
                pointsCol = document.createElement("div");
                pointsCol.classList.add("columnHeader");
                points = document.createElement("input");
                points.classList.add("input-text-field");
                points.setAttribute("type", "input");
                points.disabled = true;
                points.value = obj.points;
                pointsCol.appendChild(points);

                //Create column
                commentCol = document.createElement("div");
                commentCol.classList.add("columnHeader");
                comment = document.createElement("input");
                comment.classList.add("input-text-field");
                comment.setAttribute("type", "input");
                comment.setAttribute("name", "comment-" + obj.questionID);
                comment.disabled = true;
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
                document.body.appendChild(row);
            }
        </script>
    </body>
</html>