<?php
/**
@Prefix('faq')
*/
class QuestionController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->questions = Question::all();
	}
}