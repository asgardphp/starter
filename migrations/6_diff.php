<?php
class diff_6 {
	public static function up() {
		Schema::create('centrale', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('nom', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('magasin', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('nom', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');	
			$table->add('centrale_id', 'int(11)');
		});

		Schema::table('participant', function($table) {
			$table->col('civilite')
				->NotNullable();
		});
	}
	
	public static function down() {
		Schema::table('participant', function($table) {
			$table->col('civilite')
				->nullable();
		});
	}
}