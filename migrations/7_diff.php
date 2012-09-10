<?php
class diff_7 {
	public static function up() {
		Schema::create('jeu_magasin', function($table) {	
			$table->add('magasin_id', 'int(11)');	
			$table->add('jeu_id', 'int(11)');
		});
	}
	
	public static function down() {
		
	}
}