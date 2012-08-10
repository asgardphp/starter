<?php
class diff_14 {
	public static function up() {
		Schema::table('arpa_actualite', function($table) {
			$table->drop('titre');
		});
	}
	
	public static function down() {
		Schema::table('arpa_actualite', function($table) {
			$table->add('titre', 'text');
		});
	}
}