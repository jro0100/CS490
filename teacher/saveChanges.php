<?php
session_start();
require("./teacherutil/teacher_functions.php");
require("../util/functions.php");
redirect_to_login_if_not_valid_teacher();

if (!isset($_POST["saveChanges"]) || !isset($_POST["examID"]) || !isset($_POST["studentID"])) {
    header("Location: exams.php");
    exit();
}

$studentID = $_POST["studentID"];
$examID = $_POST["examID"];

unset($_POST["saveChanges"]);
unset($_POST["studentID"]);
unset($_POST["examID"]);

$sqlstmt = "UPDATE studenttestcases SET teacherScore = :teacherScore WHERE studentID = :studentID AND examID = :examID AND studentTestCaseID = :studentTestCaseID";
$studentTestCaseUpdateParams = array();
//$tempArray = array();
$commentArray = array();
foreach ($_POST as $key => $value) {
    $splitKey = explode("-", $key);
    $field = ":" . $splitKey[0];
    if ($splitKey[0] == "teacherComment") {
        $qID = $splitKey[1];
        $tempArray = array();
        $tempArray[$field] = $value;
        $tempArray[":questionID"] = $qID;
        $tempArray[":examID"] = $examID;
        $tempArray[":studentID"] = $studentID;
        array_push($commentArray, $tempArray);
    } elseif ($splitKey[0] == "teacherScore") {
        $studentTestCaseID = $splitKey[1];
        $tempArray = array();
        $tempArray[":teacherScore"] = $value;
        $tempArray[":studentTestCaseID"] = $studentTestCaseID;
        $tempArray[":examID"] = $examID;
        $tempArray[":studentID"] = $studentID;
        array_push($studentTestCaseUpdateParams, $tempArray);
    }
}
db_execute_query_multiple_times($sqlstmt, $studentTestCaseUpdateParams);

$sqlstmt = "UPDATE questiongrade SET teacherComment = :teacherComment WHERE studentID = :studentID AND examID = :examID AND questionID = :questionID";
db_execute_query_multiple_times($sqlstmt, $commentArray);

header("Location: reviewExam.php?examID=$examID&studentID=$studentID");
exit();

