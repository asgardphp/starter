<?php
//~ define('_ENV_', 'dev');

/* INIT */
require('coxis.php');

/* RUN */
\Coxis\Core\Router::run('Coxis\Core\Front', 'main');