<?php
\Coxis\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => 'FAQ', 'link' => 'faq');

\Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('faq/icon.svg'), 'link'=>'faq', 'title' => __('FAQ'), 'description' => __('All the questions'));