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

// Mark exam as completed for student
$sqlstmt = "UPDATE studentexam SET completedByStudent = 1 WHERE studentID = :studentID AND examID = :examID";
$params = array(":studentID" => $_SESSION["studentID"],
    ":examID" => $examID);
db_execute($sqlstmt, $params);

// ********************************************************************************************************************
// Autograding
// ********************************************************************************************************************
$currentDir = $_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"]);
if (!is_dir("autograde")) {
    mkdir($currentDir . "/autograde");
}
mkdir($currentDir . "/autograde/student" . $_SESSION["studentID"]);
chdir($currentDir . "/autograde/student" . $_SESSION["studentID"]);
$maxPointsOverall = 0;
$totalPointsScored = 0;
foreach ($_POST as $questionID => $studentAnswer) {

    // Get correct name of function that student should be defining and type of question
    $sqlstmt = "SELECT functionToCall, questionType FROM questionbank WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $results = db_execute($sqlstmt, $params)[0];
    $functionToCall = $results["functionToCall"];
    $questionType = $results["questionType"];


    // Get maximum points possible for question as given by teacher
    $sqlstmt = "SELECT maxPoints FROM questionsonexam WHERE questionID = :questionID AND examID = :examID";
    $params = array(
        ":questionID" => $questionID,
        ":examID" => $examID
    );

    $pointsScoredForQuestion = 0;
    $maxPoints = intval(db_execute($sqlstmt, $params)[0]["maxPoints"]);
    $maxPointsOverall += $maxPoints;
    $pointsForBadFunctionDef = intval(round($maxPoints / 10));
    if ($questionType != "default") {
        $pointsForQuestionConstraint = $pointsForBadFunctionDef * 2;
    }


    // Get array of testcases
    $sqlstmt = "SELECT * FROM testcases WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $testcases = db_execute($sqlstmt, $params);

    $numTests = count($testcases) - 1; // Subtract 1 so that function name test does not have same weight as real test cases
    $numCorrect = 0;
    if (isset($pointsForQuestionConstraint)) {
        $pointsPerTest = round(($maxPoints - $pointsForBadFunctionDef - $pointsForQuestionConstraint) / $numTests);
    } else {
        $pointsPerTest = round(($maxPoints - $pointsForBadFunctionDef) / $numTests);
    }


    // Check if student defined function with wrong function name, and replace it with the correct one
    $fixedFunctionName = false;
    if (!preg_match('/def (' . $functionToCall . ')\(/', $studentAnswer, $match)) {
        $fixedFunctionName = true;
        $studentFunctionDefinition = preg_match('/def (.*?)\(/', $studentAnswer, $badMatch);
        $studentFunctionDefinition = $badMatch[1];
        $guaranteedCorrectFunDefinition = preg_replace('/def (.*?)\(/', "def $functionToCall(", $studentAnswer);
    } else {
        $guaranteedCorrectFunDefinition = $studentAnswer;
        $studentFunctionDefinition = $functionToCall;
    }

    //TODO - Check if student answer compliant with constraint

    // Get databse ID of function name test case for this question
    $sqlstmt = "SELECT testCaseID FROM testcases WHERE questionID = :questionID AND answer = :answer";
    $params = array(
        ":questionID" => $questionID,
        ":answer" => $functionToCall
    );
    $functionNameTestCaseID = db_execute($sqlstmt, $params)[0]["testCaseID"];

    $insertIntoStudentTestCasesStmt = "INSERT INTO studenttestcases (examID, testCaseID, studentID, maxPoints, achievedPoints, studentOutput) VALUES (:examID, :testCaseID, :studentID, :maxPoints, :achievedPoints, :studentOutput)";
    $insertIntoStudentTestCasesParams = array();
    foreach ($testcases as $testcase) {
        if ($testcase["testCaseID"] == $functionNameTestCaseID) {
            $functionNameTestCaseParams = array(
                ":examID" => $examID,
                ":testCaseID" => $functionNameTestCaseID,
                ":studentID" => $_SESSION["studentID"],
                ":maxPoints" => $pointsForBadFunctionDef,
                ":studentOutput" => $studentFunctionDefinition
            );
            if ($fixedFunctionName) {
                $functionNameTestCaseParams[":achievedPoints"] = 0;
            } else {
                $functionNameTestCaseParams[":achievedPoints"] = $pointsForBadFunctionDef;
            }
            array_push($insertIntoStudentTestCasesParams, $functionNameTestCaseParams);
        } else {
            // Generate parameter string for test function call
            $sqlstmt = "SELECT * FROM parameters WHERE testCaseID = :testCaseID";
            $params = array(":testCaseID" => $testcase["testCaseID"]);
            $testcaseParameters = db_execute($sqlstmt, $params);
            $testCaseParamArray = array();
            foreach ($testcaseParameters as $p) {
                array_push($testCaseParamArray, $p["parameter"]);
            }
            $paramString = join(", ", $testCaseParamArray);


            // Execute student's function
            file_put_contents("test.py", $guaranteedCorrectFunDefinition . "\nprint($functionToCall($paramString))\n");
            $studentOutput = exec("python test.py");


            // Add student's output to param array to insert into database
            $testCaseOutputParams = array(
                ":examID" => $examID,
                ":testCaseID" => $testcase["testCaseID"],
                ":studentID" => $_SESSION["studentID"],
                ":maxPoints" => $pointsPerTest,
                ":studentOutput" => $studentOutput
            );
            if ($studentOutput == $testcase["answer"]) {
                $numCorrect++;
                $pointsScoredForQuestion += $pointsPerTest;
                $testCaseOutputParams[":achievedPoints"] = $pointsPerTest;
            } else {
                $testCaseOutputParams[":achievedPoints"] = 0;
            }
            array_push($insertIntoStudentTestCasesParams, $testCaseOutputParams);
        }
    }
    db_execute_query_multiple_times($insertIntoStudentTestCasesStmt, $insertIntoStudentTestCasesParams);

    // Calculate score
    if (!$fixedFunctionName) {
        $pointsScoredForQuestion += $pointsForBadFunctionDef;
    }

    //TODO - Add points scored by constraint to question score

    if ($pointsScoredForQuestion > $maxPoints) {
        $pointsScoredForQuestion = $maxPoints;
    }

    $totalPointsScored += $pointsScoredForQuestion;
    $maxPointsOverall += $maxPoints;

    // Update student's grade for question to the calculated score
    $sqlstmt = "UPDATE questiongrade SET achievedPoints = :achievedPoints, studentAnswer = :studentAnswer WHERE studentID = :studentID AND examID = :examID AND questionID = :questionID";
    $params = array(":achievedPoints" => $pointsScoredForQuestion,
        ":studentAnswer" => $studentAnswer,
        ":studentID" => $_SESSION["studentID"],
        ":examID" => $examID,
        ":questionID" => $questionID);
    db_execute($sqlstmt, $params);
}

// Add total score to studentexam table
$sqlstmt = "UPDATE studentexam SET studentGrade = :studentGrade WHERE studentID = :studentID AND examID = :examID";
$params = array(":studentGrade" => ($totalPointsScored / $maxPointsOverall) * 100,
    ":studentID" => $_SESSION["studentID"],
    ":examID" => $examID);
db_execute($sqlstmt, $params);

// Delete autograde files and directory
unlink("test.py");
rmdir("./");

//header("Location: ./");
//exit();
