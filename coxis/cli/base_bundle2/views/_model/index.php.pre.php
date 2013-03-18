<% foreach($<?php echo $bundle['model']['meta']['plural'] ?> as $<?php echo $bundle['model']['meta']['name'] ?>): %>
<a href="<% echo $this->url_for('show', array('id'=>$<?php echo $bundle['model']['meta']['name'] ?>->id)) ?>"><% echo $<?php echo $bundle['model']['meta']['name'] ?> %></a><br>
<% endforeach; %>