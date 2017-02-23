#!/usr/bin/php
<?php
/*
 * https://github.com/nikslab/blockchain-rds
 * Version 1.0: Nik Stankovic February 2017
 *
 */

require_once("include.php");

$limit = 1000;
$RECORDS = hashUnhashed($limit);
$count = count($RECORDS);
logThis(1, "Loaded $count records.");

$hashes_table = $blockchain['db_hashes_table'];

// Insert hashes into blockchain hashes table (create db transaction)
$count = 0;
$TRANSACTION = [];
$database_id = $source['db_id'];
$source_table = $source['db_table'];
foreach ($RECORDS as $key=>$hash) {
    $count++;
    $insert = "
        insert into $hashes_table
        (inserted_at, database_id, table_name, source_key, hash)
        values
        (now(), '$database_id', '$source_table', '$key', '$hash');
    ";
    $TRANSACTION[] = $insert;
}
logThis(2, "Created blockchain database transaction with $count hashes, now running it.");

// Run transaction
$pdo_blockchain->beginTransaction();
foreach ($TRANSACTION as $statement) {
    $result = $pdo_blockchain->query($statement);
}
$pdo_blockchain->commit();

logThis(2, "Hasher iteration done.");
exit(0);

////////////////////////////////////////////////////////////////////////////////

/*
 * Loads complete transactions from source and hashes them
 *
 * @return array            [primary key] = hashed record
 *
 */
function hashUnhashed($limit)
{
    global $pdo_source, $pdo_blockchain, $source, $blockchain;

    $hashes_table = $blockchain['db_hashes_table'];
    $source_table = $source['db_table'];
    $source_table_key = $source['db_table_key'];

    // Get the id of the highest item already hashed
    $select = "
        select max(cast(source_key as UNSIGNED)) as m
          from $hashes_table
         where table_name = '$source_table'
    ";
    $result = $pdo_blockchain->query($select);
    $queryData = $result->fetch(PDO::FETCH_ASSOC);
    $max_source_key = $queryData["m"];
    if ($max_source_key == "") { $max_source_key = 0; }
    logThis(3, "Max source key is $max_source_key");

    // Select max $limit of source table keys
    logThis(3, "Getting $limit records from source");
    $select = "
        select top $limit * from $source_table
         where $source_table_key > $max_source_key
    ";
    $result = $pdo_source->query($select);

    // Hash them
    $RECORDS = [];
    $MERKLE = [];
    while ($queryData = $result->fetch(PDO::FETCH_ASSOC)) {
        $id = $queryData["$source_table_key"];
        logThis(3, "Hashing $id...");
        $data = hashData(record2string($queryData));
        $RECORDS["$id"] = $data;
        $MERKLE[] = $data;
    }
    $root = merkleRoot($MERKLE);
    logThis(1, "Merkle root is $root");

    return $RECORDS;

}

?>
