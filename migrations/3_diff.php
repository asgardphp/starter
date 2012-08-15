<?php
class diff_3 {
	public static function up() {
		Schema::table('jeu', function($table) {
			$table->col('date_debut')
				->type('datetime');
			$table->col('date_fin')
				->type('datetime');
			$table->add('question_magasin', 'text');
		});
	}
	
	public static function down() {
		Schema::table('jeu', function($table) {
			$table->col('date_debut')
				->type('text');
			$table->col('date_fin')
				->type('text');
			$table->drop('question_magasin');
		});
	}
}