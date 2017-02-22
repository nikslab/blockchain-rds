<?php

$DEBUG = 3; // Global debug level.  0 means no debug, 1=info, etc. higher more info

/*
 * Connect to databases
 */

// Read in config/source.json
$source_json = file_get_contents("config/source.json");
$source = json_decode($source_json, true);

$driver = $source['db_driver'];
$host = $source['db_host'];
$port = $source['db_port'];
$db   = $source['db_name'];
$user = $source['db_user'];
$pass = $source['db_password'];
$charset = 'utf8';

$dsn = "$driver:host=$host;port=$port;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo_source = new PDO($dsn, $user, $pass, $opt);

// Read in config/blockchain.json
$blockchain_json = file_get_contents("config/blockchain.json");
$blockchain = json_decode($blockchain_json, true);

$driver = $blockchain['db_driver'];
$host = $blockchain['db_host'];
$port = $blockchain['db_port'];
$db   = $blockchain['db_name'];
$user = $blockchain['db_user'];
$pass = $blockchain['db_password'];
$charset = 'utf8';

$dsn = "$driver:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo_blockchain = new PDO($dsn, $user, $pass, $opt);


/*
 * Convert a record given as array to a string.  It's important we do this
 * consistently so that's why it's pulled into a separate function.
 * We use PHP's serialize function to do this, but you can do it differently.
 * Since this is used for hashing data, it actually doesn't matter how you
 * do it, as long as you do it consistently.
 *
 * @param array $record             Associative array of $field=>$value
 * @return string                   Array coverted to string
 */
function record2string($record)
{
    $result = serialize($record);
    return $result;
}


/*
 * Hashes input data.  We use double sha256.
 *
 * @param string $string    Data to be hashed
 * @return string           Hashed data
 *
 */
function hashData($string)
{
    $hash = hash('sha256', hash('sha256', $string));
    return $hash;
}

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
