#!/usr/bin/perl
<?php

require_once("include.php");

$RECORDS = loadUnhashed();
$count = count($RECORDS);
report(1, "Loaded $count records.");

$hashes_table = $blockchain['db_hashes_table'];

$count = 0;
foreach ($RECORDS as $key=>$record) {
    $count++;
    $hash = hashData($record);
    $insert = "
        insert into $hashes_table
        (source_key, hash)
        values
        ('$key', '$hash');
    ";
    $result = $pdo_blockchain->query($insert);
}
report(1, "Inserted $count hashes into 'hashes' table.");

report(1, "Hasher iteration done.");
exit(0);


/*
 * Loads complete transactions from source
 *
 * @return array            [primary key] = record as one long string
 *
 */
function loadUnhashed()
{
    global $pdo_source, $pdo_blockchain, $source, $blockchain;

    $hashes_table = $blockchain['db_hashes_table'];
    $source_table = $source['db_table'];
    $source_table_key = $source['db_table_key'];

    // Get the id of the highest item already hashed
    $select = "
        select max(source_key)
          from $hashes_table
         where table_name = '$source_table'
    ";
    $result = $pdo_blockchain->query($select);
    $max_source_key = "";

    // Select max 1000 of source table keys
    $select = "
        select from $source_table
         where $db_table_key > $max_source_key;
         limit 1000
    ";
    $result = $pdo_source->query($select);

}


/*
 * Hashes input data
 *
 * @param string $string    Data to be hashed
 * @return string           Hashed data
 *
 */
function hashData($string)
{
    $hash = hash('sha256', $string);
    return $hash;
}

?>
