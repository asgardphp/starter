<?php
class diff_10 {
	public static function up() {
		Schema::table('participant', function($table) {
			$table->drop('magasin');
		});
	}
	
	public static function down() {
		Schema::table('participant', function($table) {
			$table->add('magasin', 'text');
		});
	}
}