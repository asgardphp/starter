<?php
class diff_15 {
	public static function up() {
		Schema::table('arpa_actualite', function($table) {
			$table->add('titre', 'text');
		});
	}
	
	public static function down() {
		Schema::table('arpa_actualite', function($table) {
			$table->drop('titre');
		});
	}
}