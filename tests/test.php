<?php
$r = exec('phpunit CoxisTest.php', $output, $returnCode);
die('-'.$output.'-'.$returnCode.'-');