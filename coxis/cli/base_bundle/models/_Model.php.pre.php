<%
class <?php echo ucfirst($bundle['model']['meta']['name']) ?> extends \Coxis\Core\Model {
	public static $properties = array(
<?php foreach($bundle['model']['properties'] as $name=>$property): ?>
		'<?php echo $name ?>'	=>	array(
<?php foreach($property as $k=>$v): ?>
			'<?php echo $k ?>'	=>	<?php echo BuildTools::outputPHP($v) ?>,
<?php endforeach ?>
		),
<?php endforeach ?>
	);
	
	public static $relations = array(	
<?php foreach($bundle['model']['relations'] as $relationname => $relation): ?>
		'<?php echo $relationname ?>' => array(
			<?php foreach($relation as $k=>$v): ?>
			'<?php echo $k ?>'	=>	<?php echo BuildTools::outputPHP($v) ?>,
			<?php endforeach ?>
		),
<?php endforeach ?>
	);
	
	public static $behaviors = array(	
<?php foreach($bundle['model']['behaviors'] as $behaviorname => $behavior): ?>
		'<?php echo $behaviorname ?>' => <?php echo $behavior==null ? 'true':BuildTools::outputPHP($behavior) ?>,
<?php endforeach ?>
	);
		
	public static $meta = array(
<?php if(isset($bundle['model']['meta']['order_by'])): ?>
		'order_by' => '<?php echo $bundle['model']['meta']['order_by'] ?>',
<?php endif ?>
	);
	
	public function __toString() {
		return (string)$this-><?php echo $bundle['model']['meta']['name_field'] ?>;
	}

	public function url() {
		return \URL::url_for(array('<?php echo ucfirst($bundle['model']['meta']['name']) ?>', 'show'), array('id'=>$this->id));
	}
}