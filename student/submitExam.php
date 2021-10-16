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

// Create default grade of 0, to be updated when students take exam, or left at 0 if they do not
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
db_execute($sqlstmt, $params);

// Autograding
$currentDir = $_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"]);
if (!is_dir("autograde")) {
    mkdir($currentDir . "/autograde");
}
mkdir($currentDir . "/autograde/student" . $_SESSION["studentID"]);
chdir($currentDir . "/autograde/student" . $_SESSION["studentID"]);
$maxPointsOverall = 0;
$totalPointsScored = 0;
foreach ($_POST as $questionID => $studentAnswer) {
    $sqlstmt = "SELECT functionToCall FROM questionbank WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $functionToCall = db_execute($sqlstmt, $params)[0]["functionToCall"];

    echo "function: " . $functionToCall . "<br>";

    $sqlstmt = "SELECT * FROM testcases WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $testcases = db_execute($sqlstmt, $params);

    $numTests = count($testcases);
    $numCorrect = 0;

    foreach ($testcases as $testcase) {
        $sqlstmt = "SELECT * FROM parameters WHERE testCaseID = :testCaseID";
        $params = array(":testCaseID" => $testcase["testCaseID"]);
        $testcaseParameters = db_execute($sqlstmt, $params);
        $testCaseParamArray = array();
        foreach ($testcaseParameters as $p) {
            array_push($testCaseParamArray, $p["parameter"]);
        }
        $paramString = join(", ", $testCaseParamArray);
        file_put_contents("test.py", $studentAnswer . "\nprint($functionToCall($paramString))\n");
        $studentOutput = exec("python test.py");
        if ($studentOutput == $testcase["answer"]) {
            $numCorrect++;
        }
    }

    // Calculate score based on number of testcases correct
    $sqlstmt = "SELECT maxPoints FROM questionsonexam WHERE questionID = :questionID AND examID = :examID";
    $params = array(":questionID" => $questionID,
        ":examID" => $examID);
    $maxPoints = db_execute($sqlstmt, $params)[0]["maxPoints"];
    $achievedPoints = round(($numCorrect / $numTests) * intval($maxPoints));
    $totalPointsScored += $achievedPoints;
    $maxPointsOverall += $maxPoints;

    // Update student's grade for question to the calculated score
    $sqlstmt = "UPDATE questiongrade SET achievedPoints = :achievedPoints WHERE studentID = :studentID AND examID = :examID AND questionID = :questionID";
    $params = array(":achievedPoints" => $achievedPoints,
        ":studentID" => $_SESSION["studentID"],
        ":examID" => $examID,
        ":questionID" => $questionID);
    db_execute($sqlstmt, $params);
}

// Add total score to studentexam table
$sqlstmt = "UPDATE studentexam SET studentGrade = :studentGrade WHERE studentID = :studentID AND examID = :examID";
$params = array(":studentGrade" => $totalPointsScored / $maxPointsOverall,
    ":studentID" => $_SESSION["studentID"],
    ":examID" => $examID);
db_execute($sqlstmt, $params);

// Delete autograde files and directory
unlink("test.py");
rmdir("./");

header("Location: ./");
exit();
