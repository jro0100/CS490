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
    $sqlstmt = "SELECT functionToCall, questionConstraint FROM questionbank WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $results = db_execute($sqlstmt, $params)[0];
    $functionToCall = $results["functionToCall"];
    $questionConstraint = $results["questionConstraint"];


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
    if ($questionConstraint != "none") {
        $pointsForQuestionConstraint = intval(round($maxPoints / 2));
    }


    // Get array of testcases
    $sqlstmt = "SELECT * FROM testcases WHERE questionID = :questionID";
    $params = array(":questionID" => $questionID);
    $testcases = db_execute($sqlstmt, $params);

    $numTests = count($testcases) - 1; // Subtract 1 so that function name test does not have same weight as real test cases
    // Subtract 1 more to account for constraint test case matching
    if ($questionConstraint != "none") {
        $numTests -= 1;
    }
    $numCorrect = 0;
    if (isset($pointsForQuestionConstraint) && $questionConstraint != "none") {
        $pointsPerTest = intval(round(($maxPoints - $pointsForBadFunctionDef - $pointsForQuestionConstraint) / $numTests));
    } else {
        $pointsPerTest = intval(round(($maxPoints - $pointsForBadFunctionDef) / $numTests));
    }


    // Check if student defined function with wrong function name, and replace it with the correct one
    $fixedFunctionName = false;
    if (!preg_match('/def (' . $functionToCall . ')\(/', $studentAnswer, $match)) {
        $fixedFunctionName = true;
        $studentFunctionDefinition = preg_match('/def (.*?)\(/', $studentAnswer, $badMatch);
        if (count($badMatch) == 0) {
            $noFunctionDefinition = true;
            $inputFilteredForFunctionDefinition = $studentAnswer;
        } else {
            $studentFunctionDefinition = $badMatch[1];
            $inputFilteredForFunctionDefinition = preg_replace('/def (.*?)\(/', "def $functionToCall(", $studentAnswer);
        }
    } else {
        $inputFilteredForFunctionDefinition = $studentAnswer;
        $studentFunctionDefinition = $functionToCall;
    }

    // Check if constraint has been used properly
    $matchedConstraint = false;
    switch ($questionConstraint) {
        case "forLoop":
            $constraintSearchTerm = "for";
            break;
        case "whileLoop":
            $constraintSearchTerm = "while";
            break;
        case "recursion":
            $constraintSearchTerm = $functionToCall;
            break;
    }
    if (isset($constraintSearchTerm)) {
        if ($constraintSearchTerm == $functionToCall) {
            preg_match_all('/' . $constraintSearchTerm . '/', $inputFilteredForFunctionDefinition, $constraintMatches);
            if (count($constraintMatches[0]) > 1) {
                $matchedConstraint = true;
                $pointsScoredForQuestion += $pointsForQuestionConstraint;
            }
        } else {
            preg_match('/' . $constraintSearchTerm . '/', $inputFilteredForFunctionDefinition, $constraintMatches);
            if (count($constraintMatches) > 0) {
                $matchedConstraint = true;
                $pointsScoredForQuestion += $pointsForQuestionConstraint;
            }
        }
    }

    // Get database ID of function name and constraint matching test cases for this question
    $sqlstmt = "SELECT testCaseID FROM testcases WHERE questionID = :questionID AND answer = :answer";
    $params = array(array(
        ":questionID" => $questionID,
        ":answer" => $functionToCall
    ));

    array_push($params, array(
        ":questionID" => $questionID,
        ":answer" => "matchConstraint: true"
    ));

    $results = db_execute_query_multiple_times($sqlstmt, $params);
    $functionNameTestCaseID = $results[0]["testCaseID"];
    if ($questionConstraint != "none") {
        $constraintTestCaseID = $results[1]["testCaseID"];
    }

    $insertIntoStudentTestCasesStmt = "INSERT INTO studenttestcases (examID, testCaseID, studentID, maxPoints, autoGradeScore, teacherScore, studentOutput) VALUES (:examID, :testCaseID, :studentID, :maxPoints, :autoGradeScore, :teacherScore, :studentOutput)";
    $insertIntoStudentTestCasesParams = array();

    $counter = 0;
    $lastTestCase = count($testcases) - 1;
    foreach ($testcases as $testcase) {
        // Insert true or false as student's answer for constraint matching
        if (isset($constraintTestCaseID) && $constraintTestCaseID == $testcase["testCaseID"]) {
            if ($matchedConstraint) {
                $matchedConstraintString = "true";
                $constraintautogradeScore = $pointsForQuestionConstraint;
            } else {
                $matchedConstraintString = "false";
                $constraintautogradeScore = 0;
            }
            $constraintMatchTestCaseParams = array(
                ":examID" => $examID,
                ":testCaseID" => $constraintTestCaseID,
                ":studentID" => $_SESSION["studentID"],
                ":maxPoints" => $pointsForQuestionConstraint,
                ":autoGradeScore" => $constraintautogradeScore,
                ":teacherScore" => $constraintautogradeScore,
                ":studentOutput" => $matchedConstraintString
            );
            array_push($insertIntoStudentTestCasesParams, $constraintMatchTestCaseParams);

        // Insert student's function definition
        } elseif ($testcase["testCaseID"] == $functionNameTestCaseID) {

            $functionNameTestCaseScore = 0;
            if (!$fixedFunctionName) {
                $functionNameTestCaseScore = $pointsForBadFunctionDef;
            }

            if (isset($noFunctionDefinition) && $noFunctionDefinition == true) {
                $studentFunctionDefinition = "N/A";
            }

            $functionNameTestCaseParams = array(
                ":examID" => $examID,
                ":testCaseID" => $functionNameTestCaseID,
                ":studentID" => $_SESSION["studentID"],
                ":maxPoints" => $pointsForBadFunctionDef,
                ":autoGradeScore" => $functionNameTestCaseScore,
                ":teacherScore" => $functionNameTestCaseScore,
                ":studentOutput" => $studentFunctionDefinition
            );
            array_push($insertIntoStudentTestCasesParams, $functionNameTestCaseParams);
        } else {
            if ($counter == $lastTestCase) {
                $totalTally = ($numTests * $pointsPerTest) + $pointsForBadFunctionDef;
                if (isset($pointsForQuestionConstraint) && $questionConstraint != "none") {
                    $totalTally += $pointsForQuestionConstraint;
                }
                if ($totalTally > $maxPoints) {
                    $pointsPerTest = $pointsPerTest - ($totalTally - $maxPoints);
                }
            }

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
            file_put_contents("test.py", $inputFilteredForFunctionDefinition . "\nprint($functionToCall($paramString))\n");
            $studentOutput = exec("python test.py", $outputArray, $resultCode);

            // Add student's output to param array to insert into database
            $testCaseAutogradeScore = 0;
            if ($studentOutput == $testcase["answer"]) {
                $numCorrect++;
                $pointsScoredForQuestion += $pointsPerTest;
                $testCaseAutogradeScore = $pointsPerTest;
            }

            if ($resultCode != 0) {
                $studentOutput = "Error";
            }

            $testCaseOutputParams = array(
                ":examID" => $examID,
                ":testCaseID" => $testcase["testCaseID"],
                ":studentID" => $_SESSION["studentID"],
                ":maxPoints" => $pointsPerTest,
                ":autoGradeScore" => $testCaseAutogradeScore,
                ":teacherScore" => $testCaseAutogradeScore,
                ":studentOutput" => $studentOutput
            );
            array_push($insertIntoStudentTestCasesParams, $testCaseOutputParams);
        }
        $counter++;
    }
    db_execute_query_multiple_times($insertIntoStudentTestCasesStmt, $insertIntoStudentTestCasesParams);

    // Calculate score
    if (!$fixedFunctionName) {
        $pointsScoredForQuestion += $pointsForBadFunctionDef;
    }

    if ($pointsScoredForQuestion > $maxPoints) {
        $pointsScoredForQuestion = $maxPoints;
    }

    $totalPointsScored += $pointsScoredForQuestion;

    // Update student's grade for question to the calculated score
    $sqlstmt = "UPDATE questiongrade SET achievedScore = :achievedPoints, studentAnswer = :studentAnswer WHERE studentID = :studentID AND examID = :examID AND questionID = :questionID";
    $params = array(":achievedPoints" => $pointsScoredForQuestion,
        ":studentAnswer" => $studentAnswer,
        ":studentID" => $_SESSION["studentID"],
        ":examID" => $examID,
        ":questionID" => $questionID);
    db_execute($sqlstmt, $params);
}

// Add total score to studentexam table
$sqlstmt = "UPDATE studentexam SET studentGrade = :studentGrade WHERE studentID = :studentID AND examID = :examID";
$params = array(":studentGrade" => $totalPointsScored,
    ":studentID" => $_SESSION["studentID"],
    ":examID" => $examID);
db_execute($sqlstmt, $params);

// Delete autograde files and directory
unlink("test.py");
rmdir("./");

header("Location: ./");
exit();
