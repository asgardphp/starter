<% foreach($<?php echo $bundle['model']['meta']['plural'] ?> as $<?php echo $bundle['model']['meta']['name'] ?>): %>
<a href="<% echo $this->url_for('show', array('id'=>$<?php echo $bundle['model']['meta']['name'] ?>->id)) ?>"><h1><% echo $<?php echo $bundle['model']['meta']['name'] ?> %></h1></a>
<% endforeach; %>