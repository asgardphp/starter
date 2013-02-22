<?php
\Coxis\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => __('Users'), 'link' => 'users');

\Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('intranet/icon.svg'), 'link'=>'users', 'title' => __('Users'), 'description' => __('Manage your users'));