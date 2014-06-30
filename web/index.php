<?php
require_once '../autoload.php'; #composer autoloader

/* RUN & SEND */
$kernel = new Kernel();
$kernel->load();
$kernel->getContainer()['httpKernel']->run()->send();