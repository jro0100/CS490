<?php

/**
 * Establishes a PDO connection to a MYSQL database using the credentials stored in environment variables
 * @return PDO|null
 */
function get_db() {
    global $db;
    if (!isset($db)) {
        try {
            $dbhost = getenv("DB_HOSTNAME");
            $dbname = getenv("DB_NAME");
            $dbuname = getenv("DB_USERNAME");
            $dbpass = getenv("DB_PASSWORD");

            $con = "mysql:dbname=$dbname;host=$dbhost";
            $db = new PDO($con, $dbuname, $dbpass);
        } catch (Exception $e) {
            var_export($e);
            $db = null;
        }
    }
    return $db;
}

/**
 * Executes a given SQL statement with given parameters and returns the result if any, or void if none
 * @param $stmtstring string SQL query string to be sent to the database
 * @param $params array Associative array where the keys are the variables in the SQL statement string and the values are the values to be inserted into those variables
 * @return mixed|void
 */
function db_execute($stmtstring, $params) {
    $db = get_db();
    $stmt = $db->prepare($stmtstring);
    $stmt->execute($params);
    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        var_export($e);
        exit("Database failure try again");
    }
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $result;
}

