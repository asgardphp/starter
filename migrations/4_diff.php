<?php
class diff_4 {
	public static function up() {
		Schema::table('jeu', function($table) {
			$table->col('question_magasin')
				->NotNullable();
		});

		Schema::table('participant', function($table) {
			$table->col('email')
				->type('varchar(255)');
			$table->add('jeu_id', 'int(11)');
		});
	}
	
	public static function down() {
		Schema::table('jeu', function($table) {
			$table->col('question_magasin')
				->nullable();
		});

		Schema::table('participant', function($table) {
			$table->col('email')
				->type('text');
			$table->drop('jeu_id');
		});
	}
}