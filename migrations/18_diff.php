<?php
class diff_18 {
	public static function up() {
		Schema::table('arpa_administrator', function($table) {
			$table->add('created_at', 'datetime');
			$table->add('updated_at', 'datetime');
		});

		Schema::table('arpa_value', function($table) {
			$table->col('key')
				->NotNullable();
			$table->col('value')
				->NotNullable();
		});

		Schema::create('arpa_mailing', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('titre', 'text');	
			$table->add('contenu', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});
	}
	
	public static function down() {
		Schema::table('arpa_administrator', function($table) {
			$table->drop('created_at');
			$table->drop('updated_at');
		});

		Schema::table('arpa_value', function($table) {
			$table->col('key')
				->nullable();
			$table->col('value')
				->nullable();
		});
	}
}