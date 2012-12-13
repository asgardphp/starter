<?php
\Schema::dropAll();
ORMManager::autobuild();
\BundlesManager::loadModelFixturesAll();
#or
ORMManager::loadModelFixtures('admin.models.yml');
