<?php
\Coxis\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => 'Actualites', 'link' => 'actualites');

\Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('actualite/icon.svg'), 'link'=>'actualites', 'title' => __('News'), 'description' => __('Les actualités'));