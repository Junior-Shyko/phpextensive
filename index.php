<?php 
require __DIR__.'/vendor/autoload.php';

use JuniorShyko\Phpextensive\Extensive;

$br = "\n";

$e = new Extensive();
echo $e->extensive( 1001 );
echo $br;
echo $e->extensive( 1001, Extensive::COIN );
echo $br;
print_r($e->extensive( 54001.99, Extensive::MALE_NUMBER ));
echo $br;
echo $e->extensive( 1005 );
echo $br;
echo $e->extensive( 185001.084 );
echo $br;
echo $e->extensive( 4001.17, Extensive::MALE_NUMBER  );
echo $br;
echo $br;

?>