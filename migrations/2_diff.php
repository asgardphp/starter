<?php
class diff_2 {
	public static function up() {
		Schema::create('participant', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('nom', 'text');	
			$table->add('prenom', 'text');	
			$table->add('adresse', 'text');	
			$table->add('code_postal', 'text');	
			$table->add('ville', 'text');	
			$table->add('pays', 'text');	
			$table->add('email', 'text');	
			$table->add('telephone', 'text');	
			$table->add('reponse', 'text');	
			$table->add('magasin', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});
	}
	
	public static function down() {
		
	}
}