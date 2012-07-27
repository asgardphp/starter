<?php
class TestController extends Controller {
	/**
	@Route('trad')
	*/
	public function tradAction($request) {
		Coxis::set('layout', false);
		echo 'test: ';
		
		//~ echo __('Hello :name!', array('name'=>'Michel'));
		echo __('Here I am');
	}

	/**
	@Route('test')
	*/
	public function testAction($request) {
		$form = new ModelForm(new Foo);

		$form->setCallback('pre_test', function($form, $data) {
			if(($day=$form->naissance->day->value) && ($month=$form->naissance->month->value) && ($year=$form->naissance->year->value))
				$data['date_naissance'] = $day.'/'.$month.'/'.$year;
				
			return $data;
		});

		$form->addWidgets(array(
			'naissance'		=>	array(
				'day'			=>	new DayWidget,
				'month'		=>	new MonthWidget,
				'year'		=>	new YearWidget,
			),
			//~ 'naissance'		=>	new DateGroup,
			'confirm_mot_de_passe'	=>	new Widget(array(
				'validation'		=>	'same:mot_de_passe',#laravel
				'messages'		=>	array(
					'same'	=>	'Les mots de passe doivent être identiques.',
				),
			)),
			'confirm_email'		=>	new Widget(array(
				'validation'		=>	'same:email',#laravel
				'messages'		=>	array(
					'same'	=>	'Les adresses email doivent être identiques.',
				),
			)),
			'conditions'			=>	new Widget(array(
				'validation'		=>	'required',
				'messages'		=>	array(
					'true'		=>	'Vous devez accepter les conditions d\'utilisation du site.',
				),
			)),
		));

		if($form->isSent()) {
			try {
				$form->save();
				Flash::addSuccess('Votre inscription a bien été prise en compte.');
			} catch(\Coxis\Core\Form\FormException $e)  {
				Flash::addError($e->errors);
			}
		}
		
		#####
		Response::setHeader('Content-Type', 'text/html; charset=utf-8');
		Flash::showAll();
		$form->start();
		//~ $form->naissance->date_naissance->input();
		echo 'Birthday : ';
		$form->naissance->day->select();
		$form->naissance->month->select();
		$form->naissance->year->select();
		echo '<br/>';
		echo 'Password : ';
		$form->mot_de_passe->input();
		echo '<br/>';
		echo 'Confirm Password : ';
		$form->confirm_mot_de_passe->input();
		echo '<br/>';
		echo 'Email : ';
		$form->email->input();
		echo '<br/>';
		echo 'Confirm Email : ';
		$form->confirm_email->input();
		echo '<br/>';
		echo 'Conditions generales : ';
		$form->conditions->checkbox();
		echo '<br/>';
		echo 'Image : ';
		$form->image->file();
		echo '<br/>';
		echo '<input type="submit"/>';
		$form->end();
		Coxis::set('layout', false);
	}
}