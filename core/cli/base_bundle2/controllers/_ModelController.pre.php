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
			$this->forward404();
			
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		
		//~ HTML::setTitle($this->page->meta_title!='' ? $this->page->meta_title:$this->page->title);
		//~ HTML::setKeywords($this->page->meta_keywords);
		//~ HTML::setDescription($this->page->meta_description);
	}
}