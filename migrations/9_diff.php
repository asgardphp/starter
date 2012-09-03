<?php
class diff_9 {
	public static function up() {
		Schema::table('participant', function($table) {
			$table->col('magasin_id')
				->NotNullable();
		});
	}
	
	public static function down() {
		Schema::table('participant', function($table) {
			$table->col('magasin_id')
				->nullable();
		});
	}
}