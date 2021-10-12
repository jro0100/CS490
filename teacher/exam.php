<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (isset($_POST["saveExam"])) {
    unset($_POST["saveExam"]);

    // Save and unset exam name from POST array to easily iterate over array to pull out question ID's
    $examName = $_POST["examName"];
    unset($_POST["examName"]);

    $examID = "holder";
    $sqlstmt = "INSERT INTO exams (teacherID, examName, released, gradedByTeacher) VALUES (:teacherID, :examName, 0, 0)";
    $params = array(":teacherID" => $_SESSION["teacherID"],
        "examName" => $examName);
    db_execute($sqlstmt, $params, $examID);

    $sqlstmt = "INSERT INTO questionsonexam (questionID, examID, maxPoints) VALUES (:questionID, :examID, :maxPoints)";
    $params = array();
    foreach ($_POST as $key => $val) {
        $questionID = explode("-", $key)[0];
        array_push($params, array(
            ":questionID" => $questionID,
            ":examID" => $examID,
            ":maxPoints" => $val
        ));
    }
    db_execute_query_multiple_times($sqlstmt, $params);
    header("Location: exam.php?examID=" . $examID);
    exit();
} elseif (!isset($_GET["examID"])) {
    header("Location: examList.php");
    exit();
}

$sqlstmt = "SELECT * FROM exams WHERE examID = :examID AND teacherID = :teacherID";
$params = array(":examID" => $_GET["examID"],
    ":teacherID" => $_SESSION["teacherID"]);
$result = db_execute($sqlstmt, $params)[0];

if ($result) {
    $json = json_encode($result);
} else {
    header("Location: examList.php");
    exit();
}

?>