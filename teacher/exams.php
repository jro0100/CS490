<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet"  href="../css/menu.css">
    <link rel="stylesheet"  href="../css/main.css">
    <link rel="stylesheet"  href="../css/teacher/exams.css">
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
        var text = [{"examID":"5","teacherID":"5","examName":"Test Exam","released":"0","gradedByTeacher":"0"},{"examID":"15","teacherID":"5","examName":"Test 2","released":"0","gradedByTeacher":"0"},{"examID":"25","teacherID":"5","examName":"Test Exam Again","released":"0","gradedByTeacher":"0"}];

        for (i = 0; i < text.length; i++) {

            const obj = JSON.parse(JSON.stringify(text[i]));

            //Create row
            row = document.createElement("div");
            row.classList.add("row");

            //Create column
            column = document.createElement("div");
            column.classList.add("column");

            exam = document.createElement("p");
            exam.classList.add("center-column-text");
            exam.innerHTML = obj.examName;

            centerDiv = document.createElement("div");
            centerDiv.classList.add("center");
            form = document.createElement("form");
            form.setAttribute("method", "get");
            form.setAttribute("action", "exams.php");
            submit = document.createElement("button");
            submit.setAttribute("type", "submit");
            submit.setAttribute("name", "releaseExam");
            submit.setAttribute("value", obj.examID);
            submit.innerHTML = "Release Exam";
            
            //Add review exam button
            
            column.appendChild(exam);
            form.appendChild(submit);
            centerDiv.appendChild(form);
            column.appendChild(centerDiv) //Add button to release the exam

            row.appendChild(column);
            document.body.appendChild(row); //Appends the div to the body of the HTML page
        }

        if (text.length == 0) {
            emptiness = document.createElement("div");
            emptiness.classList.add("center-column-text");
            emptiness.innerHTML = "NO EXAMS EXIST YET!";
            document.body.appendChild(emptiness);
        }
    </script>
</body>
</html>