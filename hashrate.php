#!/usr/bin/php
<?php

$test = 4000000;
print "Generating $test random strings first...\n";
$DATA = [];
for($i=1; $i <= $test; $i++) {
    $DATA[] = rand_string(20);
}

print "Now hashing...\n";
$start = time();
foreach ($DATA as $string) {
    $hash = hash("sha256", $string);
}
$stop = time();
$elapsed = $stop - $start;
$per_second = round($test / $elapsed, 2);
print "Done.  Took $elapsed seconds to hash $test hashes.\nHash rate is $per_second hashes per second.\n";


function rand_string($length) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	$size = strlen( $chars );
    $str = "";
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
	}

	return $str;
}
