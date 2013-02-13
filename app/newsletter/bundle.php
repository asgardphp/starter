<?php
AdminMenu::$menu[0]['childs'][] = array('label' => 'Newsletter', 'link' => 'newsletter');

\Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('newsletter/icon.svg'), 'link'=>'newsletter', 'title' => __('Newsletter'), 'description' => 'Send mailings to your subscribers!');

AdminMenu::$menu[0]['childs'][] = array('label' => 'Subscribers', 'link' => 'subscribers');

\Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('newsletter/subscribers-icon.svg'), 'link'=>'subscribers', 'title' => __('Subscribers'), 'description' => 'All your newsletter subscribers.');