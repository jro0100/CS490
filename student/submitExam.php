<?php
session_start();
require("./studentutil/student_functions.php");
require ("../util/functions.php");
redirect_to_login_if_not_valid_student();

if (!isset($_POST["submitExam"])) {
    header("Location: ./");
    exit();
}

unset($_POST["submitExam"]);

if (isset($_POST["examID"])) {
    $examID = $_POST["examID"];
    unset($_POST["examID"]);
} else {
    header("Location: ./");
    exit();
}

$sqlstmt = "INSERT INTO questiongrade (studentID, examID, questionID, studentAnswer) VALUES (:studentID, :examID, :questionID, :studentAnswer)";
$params = array();
foreach ($_POST as $questionID => $studentAnswer) {
    array_push($params, array(":studentID" => $_SESSION["studentID"],
        ":examID" => $examID,
        ":questionID" => $questionID,
        ":studentAnswer" => $studentAnswer));
}
db_execute_query_multiple_times($sqlstmt, $params);

// Mark exam as completed for student
$sqlstmt = "UPDATE studentexam SET completedByStudent = 1 WHERE studentID = :studentID AND examID = :examID";
$params = array(":studentID" => $_SESSION["studentID"],
    ":examID" => $examID);
//db_execute($sqlstmt, $params);

// Autograding
$currentDir = $_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"]);
mkdir($currentDir . "/autograde/" . $_SESSION["studentID"]);
chdir("autograde/" . $_SESSION["studentID"]);
foreach ($_POST as $questionID => $studentAnswer) {
    $sqlstmt = "SELECT functionToCall FROM questionbank WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $functionToCall = db_execute($sqlstmt, $params)[0]["functionToCall"];

    echo "function: " . $functionToCall . "<br>";

    $sqlstmt = "SELECT * FROM testcases WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $testcases = db_execute($sqlstmt, $params);
    $numTests = count($testcases);

    foreach ($testcases as $testcase) {
        $sqlstmt = "SELECT * FROM parameters WHERE testCaseID = :testCaseID";
        $params = array(":testCaseID" => $testcase["testCaseID"]);
        $testcaseParameters = db_execute($sqlstmt, $params);
        $testCaseParamArray = array();
        foreach ($testcaseParameters as $p) {
            array_push($testCaseParamArray, $p["parameter"]);
        }
        $paramString = join(", ", $testCaseParamArray);
        echo "Parameters: " . $paramString . "<br>";
        echo "Answer: " . $testcase["answer"] . "<br><br>";
        //echo $paramString;
        //var_export($testcaseParameters);
        //TODO Create parameter string and run each test case
        echo "<br>";
        exec("echo $studentAnswer print($functionToCall($paramString)) > test.py");
        //exec("echo print($functionToCall($paramString)) >> test.py");
    }
    echo "<br><br>";
    //echo $paramString;

}
