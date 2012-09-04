<?php
class diff_8 {
	public static function up() {
		Schema::table('participant', function($table) {
			$table->add('magasin_id', 'int(11)');
		});
	}
	
	public static function down() {
		Schema::table('participant', function($table) {
			$table->drop('magasin_id');
		});
	}
}