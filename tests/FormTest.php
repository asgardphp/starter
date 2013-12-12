<?php
if(!defined('_ENV_'))
	define('_ENV_', 'test');
require_once(_CORE_DIR_.'core.php');
\Coxis::load();

class FormTest extends PHPUnit_Framework_TestCase {
	/*public function setUp(){
		App::newDefault();

		POST::set('publish_date', '20/09/1988');

		Form::setDefaultRanderCallback('date', function($field, $options=array()) {
			$day = $field->getDay();
			$month = $field->getMonth();
			$year = $field->getYear();

			$widget = new SelectWidget($field->getName().'[day]', $day, array_combine(range(1, 31), range(1, 31)));
			$widget->render();
			$widget = new SelectWidget($field->getName().'[month]', $month, array_combine(range(1, 12), range(1, 12)));
			$widget->render();
			$widget = new SelectWidget($field->getName().'[year]', $year, array_combine(range(date('Y'), date('Y')-50), range(date('Y'), date('Y')-50)));
			$widget->render();
		});

		Form::setDefaultRanderCallback('text', function($field, $options=array()) {
			$widget = new TextWidget($field->getName(), $field->getValue());
			$widget->render();
		});

		Form::setDefaultRanderCallback('hidden', function($field, $options=array()) {
			$widget = new TextWidget($field->getName(), $field->getValue());
			$widget->render();
		});
	}*/

	public function tearDown(){}

	public function test0() {
	}

	/*public function test1() {
		$form = new Form;
		$options = array();
		$form->publish_date = new DateField($options);
		// $form->publish_date->render();
		// $form->publish_date->render('date');
		// $form->publish_date->date();
		$form->publish_date->def();
	}

	public function test2() {
		$form = new Form;
		$options = array();
		$form->publish_date = new DateField($options);
		echo $form->publish_date->getValue()->format('d/m/Y');
	}

	public function test3() {
		$form = new Form;
		$options = array(
			'data_type' => 'date',
		);
		$form->publish_date = new DateField($options);
		echo $form->publish_date->getValue()->format('d/m/Y');
	}

	public function test4() {
		$form = new Form;
		$options = array(
			'data_type' => 'string',
		);
		$form->publish_date = new DateField($options);
		echo $form->publish_date->getValue();
	}

	public function test5() {
		$form = new Form;
		$form->_csrf_token->def();
	}

	public function test6() {
		Request::setMethod('post');
		// // Session::set('_csrf_token', '1234678');
		POST::set('_csrf_token', '1234678');
		$form = new Form;
		$errors = $form->errors();
		var_dump($errors);
	}

	public function test7() {
		Request::setMethod('post');
		POST::set('captcha', '1234678');
		$form = new Form;
		$form->captcha = new CaptchaField;
		$errors = $form->errors();
		$form->captcha->def();
		var_dump($errors);
	}*/
}
