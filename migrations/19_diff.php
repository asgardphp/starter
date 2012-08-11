<?php
class diff_19 {
	public static function up() {
		Schema::table('arpa_administrator', function($table) {
			$table->col('created_at')
				->NotNullable();
			$table->col('updated_at')
				->NotNullable();
		});
	}
	
	public static function down() {
		Schema::table('arpa_administrator', function($table) {
			$table->col('created_at')
				->nullable();
			$table->col('updated_at')
				->nullable();
		});
	}
}