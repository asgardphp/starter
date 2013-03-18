<%
\Coxis\Admin\Libs\AdminMenu::$menu[0]['childs'][] = array('label' => '<?php echo ucfirst($bundle['model']['meta']['label_plural']) ?>', 'link' => '<?php echo $bundle['model']['meta']['plural'] ?>');

// \Coxis\Admin\Libs\AdminMenu::$home[] = array('img'=>\URL::to('<?php echo $bundle['name'] ?>/icon.svg'), 'link'=>'<?php echo $bundle['model']['meta']['plural'] ?>', 'title' => '', 'description' => '');