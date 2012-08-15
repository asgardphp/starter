<?php
class diff_1 {
	public static function up() {
		Schema::create('administrator', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('username', 'varchar(100)');	
			$table->add('password', 'varchar(100)');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('value', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('key', 'text');	
			$table->add('value', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('jeu', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('titre', 'text');	
			$table->add('couleur_de_fond', 'text');	
			$table->add('adresse', 'text');	
			$table->add('date_debut', 'text');	
			$table->add('date_fin', 'text');	
			$table->add('question', 'text');	
			$table->add('reponses', 'text');	
			$table->add('bonne_reponse', 'text');	
			$table->add('codes_barres', 'text');	
			$table->add('lien_optionnelle', 'text');	
			$table->add('magasins', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');	
			$table->add('filename_image_de_fond', 'text');	
			$table->add('filename_valider', 'text');	
			$table->add('filename_reglement_du_jeu', 'text');	
			$table->add('filename_pdf', 'text');	
			$table->add('filename_image_optionnelle', 'text');
		});
	}
	
	public static function down() {
		
	}
}