<?php
class diff_5 {
	public static function up() {
		Schema::table('participant', function($table) {
			$table->add('civilite', 'text');
			$table->col('code_postal')
				->type('int(5)');
			$table->col('jeu_id')
				->NotNullable();
		});
	}
	
	public static function down() {
		Schema::table('participant', function($table) {
			$table->col('code_postal')
				->type('text');
			$table->col('jeu_id')
				->nullable();
			$table->drop('civilite');
		});
	}
}