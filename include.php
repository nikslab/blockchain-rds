<?php
/*
 * https://github.com/nikslab/blockchain-rds
 * Version 1.0: Nik Stankovic February 2017
 *
 */

$DEBUG = 3; // Global debug level.  0 means no debug, higher more info.  Used for logging.

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
logThis(3, "Connected to source database");

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
logThis(3, "Connected to blockchain database");


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
 * Stolen from http://bitcoin.stackexchange.com/questions/43565/how-can-i-make-this-php-merkle-root-script-recursive
 */
function binFlipByteOrder($string) {
    return implode('', array_reverse(str_split($string, 1)));
}


/*
 * Stolen from http://bitcoin.stackexchange.com/questions/43565/how-can-i-make-this-php-merkle-root-script-recursive
 */
function merkleRootRecursive($txids) {

    // Check for when the result is ready, otherwise recursion
    if (count($txids) === 1) {
        return $txids[0];
    }

    // Calculate the next row of hashes
    $pairhashes = [];
    while (count($txids) > 0) {
        if (count($txids) >= 2) {
            // Get first two
            $pair_first = $txids[0];
            $pair_second = $txids[1];

            // Hash them
            $pair = $pair_first.$pair_second;
            $pairhashes[] = hash('sha256', hash('sha256', $pair, true), true);

            // Remove those two from the array
            unset($txids[0]);
            unset($txids[1]);

            // Re-set the indexes (the above just nullifies the values) and make a new array without the original first two slots.
            $txids = array_values($txids);
        }

        if (count($txids) == 1) {
            // Get the first one twice
            $pair_first = $txids[0];
            $pair_second = $txids[0];

            // Hash it with itself
            $pair = $pair_first.$pair_second;
            $pairhashes[] = hash('sha256', hash('sha256', $pair, true), true);

            // Remove it from the array
            unset($txids[0]);

            // Re-set the indexes (the above just nullifies the values) and make a new array without the original first two slots.
            $txids = array_values($txids);
        }
    }

    return merkleRootRecursive($pairhashes);
}


function merkleRoot($txids)
{
    $txidsBEbinary = [];
    foreach ($txids as $txidBE) {
        // covert to binary, then flip
        $txidsBEbinary[] = binFlipByteOrder(hex2bin($txidBE));
    }
    $root = merkleRootRecursive($txidsBEbinary);

    return bin2hex(binFlipByteOrder($root)) . PHP_EOL;
}


function mine($merkle, $target)
{
    $nonce = 0;

    $found = false;
    while (!$found) {
        $found = hashData($merkle . $nonce) < $target;
    }

    return $nonce;
}


/*
 * Debuging.  Will print $string to screen if $level is greater global $DEBUG
 *
 * @param integer $level    Debug level of this message
 * @param string $string    Debug message
 *
 */
function logThis($level, $string)
{
    global $DEBUG;

    // Printing it out to stdout, but if you want to log, change it here
    $stamp = date('Y-m-d H:i:s', time());
    if ($level <= $DEBUG) {
        print $stamp . " | " . $level . " | " . $string . "\n";
    }
}
