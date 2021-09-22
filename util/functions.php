<?php
function get_db() {
    global $db;
    if (!isset($db)) {
        try {
            require('dbcredentials.php');

            $con = "mysql:dbname=$dbname;host=$dbhost";
            $db = new PDO($con, $dbuname, $dbpass);
        } catch (Exception $e) {
            var_export($e);
            $db = null;
        }
    }
    return $db;
}

function db_execute($stmtstring, $params) {
    $db = get_db();
    $stmt = $db->prepare($stmtstring);
    $stmt->execute($params);
    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        var_export($e);
        exit("Database failure try again");
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $result;
}

