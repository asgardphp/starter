<%
/**
@Prefix('<?php echo $bundle['model']['meta']['plural'] ?>')
*/
class <?php echo ucfirst($bundle['model']['meta']['name']) ?>Controller extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this-><?php echo $bundle['model']['meta']['plural'] ?> = <?php echo ucfirst($bundle['model']['meta']['name']) ?>::all();
	}

	/**
	@Route(':id')
	*/
	public function showAction($request) {
		if(!($this-><?php echo $bundle['model']['meta']['name'] ?> = <?php echo ucfirst($bundle['model']['meta']['name']) ?>::load($request['id'])))
			$this->notfound();
			
		// $this-><?php echo $bundle['model']['meta']['name'] ?>->showMetas();
		// SEO::canonical($this, $this-><?php echo $bundle['model']['meta']['name'] ?>->url());
	}
}