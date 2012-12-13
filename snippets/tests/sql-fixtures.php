<?php
\Schema::dropAll();
ORMManager::autobuild();
\DB::import('tests/coxis.sql');
