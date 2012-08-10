<?php
class diff_17 {
	public static function up() {
		Schema::table('arpa_actualite', function($table) {
			$table->col('lieu')
				->def(false);
		});
	}
	
	public static function down() {
		Schema::table('arpa_actualite', function($table) {
			$table->col('lieu')
				->def('aaa');
		});
	}
}