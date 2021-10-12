<?php


$questionID = 15;
$_POST["functionToCall"] = "addtwo";

$out = array();
//echo $_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"]);

$currentDir = $_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"]);


$execPrefix = "";
if (php_uname("s") != "Windows NT") {
    $execPrefix = "./";
}
mkdir($currentDir . "/" . $questionID);
mkdir($currentDir . "/" . $questionID . "/studentanswer");
//exec("cd testdir && echo hello world > a.txt");
//exec("echo test", $out);
exec($execPrefix . "maketestcasefile.sh 15 addfour");
exec($execPrefix . "populateunittests.sh 1 5 4 3 addfour 13 1 15", $out);
exec($execPrefix . "populateunittests.sh 72 3 65 19 addfour 159 2 15", $out);
exec("addclosingtag.sh 15");
var_export($out);
//echo "it work?";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<script type="text/javascript">
    //const test = "";
    //const obj = JSON.parse(test);
    //console.log(test);
</script>

</body>
</html>
