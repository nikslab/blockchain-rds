<?php

$DEBUG = 3; // Global debug level.  0 means no debug, 1=info, etc.

/*
 * Connect to databases
 */

// Read in config/source.json
$source_json = file_get_contents("config/source.json");
$source = json_decode($source_json, true);

$host = $source['db_host'];
$db   = $source['db_name'];
$user = $source['db_user'];
$pass = $source['db_pass'];
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo_source = new PDO($dsn, $user, $pass, $opt);

// Read in config/blockchain.json
$blockchain_json = file_get_contents("config/blockchain.json");
$blockchain = json_decode($source_json, true);

$host = $blockchain['db_host'];
$db   = $blockchain['db_name'];
$user = $blockchain['db_user'];
$pass = $blockchain['db_pass'];
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo_blockchain = new PDO($dsn, $user, $pass, $opt);

/*
 * Debuging.  Will print $string to screen if $level is greater global $DEBUG
 *
 * @param number $level         Debug level of this message
 * @param string $string        Debug message
 *
 */
function report($level, $string)
{
    global $DEBUG;

    $stamp = date('Y-m-d H:i:s', time());
    if ($level >= $DEBUG) {
        print $stamp . " | " . $debug . " | " . $string . "\n";
    }
}
