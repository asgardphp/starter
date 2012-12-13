<?php
	/**
	@Route('depot-choeur')
	*/
	public function depot_choeurAction($request) {
		//~ $this->canonical(url_for(array('page', 'show'), array('id' => $this->page->id, 'slug' => $this->page->slug)));
		//~ Metas::set($this->annonce);
		
		$choeur = Choeur::create();
		$this->form = new ModelForm($choeur);
		
		
		if($this->form->isSent()) {
			try {
				$this->form->save();
				Response::setCode(200)->send();
			}
			catch(FormException $e) {
				Response::setCode(500)->sendHeaders();
				foreach($e->errors as $err)
					echo $err."\n";
				Response::send();
			}
		}
	}