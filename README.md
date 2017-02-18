# blockchain-rds
Blockchain your RDS table records with Bitcoin-style Proof of Work to ensure data is complete and has not been tampered with

## blockchain.sql
We keep the blockchain as a MySQL database.  It needs two two tables: *hashes* and *headers* as defined in **blockchain.sql** which will help you create them.  You can keep this in the same database as your source data, or a separate database on the same server, or a separate database on a separate server or as part of a bigger database on a separate server.  There are little to no security considerations for these two tables, other than you don't want to lose them.

## Scripts

### hasher.php
Hasher simply hashes records from the source table and inserts them into the *blockchain* table *hashes*.  You need to tell the hasher.php which records are "complete" or ok to hash, meaning they will no longer change.  Depending on how your source database is structured, records may not be complete the moment they are inserted, but get updated later.  For example, a record may be complete after it has been "approved".  You need to define a SQL statement for the hasher.php so it knows to hash only those records which are "complete".

### miner.php
This script does Proof of Work and creates blocks the way Bitcoin mining works.  Once it finds the hash of appropriate difficulty it will create a block record, and mark all the hashes in the *hashes* table which belong to the block.

Details of Bitcoin blockchain hashing: https://en.bitcoin.it/wiki/Block_hashing_algorithm

### validator.php
Checks integrity of database.  Returns 0 (check failed) or 1 (check passed).

### agent.php
Runs one of the above scripts in a loop over one minute, then dies.  Should be started as a cron job every minute.

## /config directory

