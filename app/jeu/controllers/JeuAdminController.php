<?php
/**
@Prefix('admin/jeux')
*/
class JeuAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'jeu';
	static $_models = 'jeux';
	
	static $_messages = array(
			'modified'			=>	'Jeu modifié avec succès.',
			'created'			=>	'Jeu créé avec succès.',
			'many_deleted'			=>	'Jeux supprimés avec succès.',
			'deleted'			=>	'Jeu modifié avec succès.',
			'unexisting'			=>	'Ce jeu n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		unset($form->participants);
		
		return $form;
	}
	
	/**
	@Route(':id/export');
	*/
	public function exportAction($request) {
		$this->jeu = new Jeu($request['id']);
		
		if($_POST) {
			$q = $this->jeu->participants();
			//~ d($q->get());
			if($_POST['magasin'])
				$q->where(array('magasin_id'=>$_POST['magasin']));
			if($_POST['type'] == 'Gagants')
				$q->where(array('reponse'=>$this->jeu->bonne_reponse));
			$all = $q->get();
			//~ d($all, $q->dal()->where);
			
			#CSV
			$result = 'Nom;Prenom;Adresse;CodePostal;Ville;Pays;Email;Telephone;Reponse;Magasin'."\n";
			foreach($all as $one)
				$result .= $one->nom.';'.$one->prenom.';'.$one->adresse.';'.$one->code_postal.';'.$one->ville.';'.$one->pays.';'.$one->email.';'.$one->telephone.';'.$one->reponse.';'.$one->magasin."\n";
			
			Response::setHeader('Content-Type', 'text/csv')->setHeader('content-disposition', 'attachment; filename="participants.csv"')->setContent($result)->send();
		}
	}
}