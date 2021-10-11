<?php


$questionID = 5;
$_POST["functionToCall"] = "addtwo";

$out = array();
exec("maketestcasefile.sh 15 addfour");
exec("populateunittests.sh 1 5 4 3 addfour 13 1 15", $out);
exec("populateunittests.sh 72 3 65 19 addfour 159 2 15", $out);
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
