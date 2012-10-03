<?php
class diff_2 {
	public static function up() {
		Schema::table('arpa_administrator', function($table) {
			$table->col('id');
		});

		Schema::table('arpa_value', function($table) {
			$table->col('id');
		});

		Schema::table('arpa_actualite', function($table) {
			$table->col('id');
		});

		Schema::table('arpa_commentaire', function($table) {
			$table->col('id');
		});
	}
	
	public static function down() {
		Schema::create('arpa_actualite_commentaire', function($table) {	
			$table->add('actualite_id', 'int(11)');	
			$table->add('commentaire_id', 'int(11)');
		});

		Schema::create('arpa_actualite_translation', function($table) {	
			$table->add('id', 'int(11)')
				->primary();	
			$table->add('locale', 'varchar(10)')
				->primary();	
			$table->add('test', 'text');
		});

		Schema::create('arpa_annonce', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('intitule', 'text')
				->nullable();	
			$table->add('categorie', 'text')
				->nullable();	
			$table->add('region', 'text')
				->nullable();	
			$table->add('adresse', 'text')
				->nullable();	
			$table->add('ville', 'text')
				->nullable();	
			$table->add('code_postal', 'text')
				->nullable();	
			$table->add('contenu', 'varchar(600)');	
			$table->add('nom', 'text')
				->nullable();	
			$table->add('prenom', 'text')
				->nullable();	
			$table->add('portable', 'text')
				->nullable();	
			$table->add('telephone', 'text')
				->nullable();	
			$table->add('email', 'text')
				->nullable();	
			$table->add('site_web', 'text')
				->nullable();	
			$table->add('slug', 'text')
				->nullable();	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('arpa_article', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('title', 'varchar(100)');
		});

		Schema::create('arpa_article_author', function($table) {	
			$table->add('article_id', 'int(11)')
				->primary();	
			$table->add('author_id', 'int(11)')
				->primary();
		});

		Schema::create('arpa_author', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('name', 'varchar(100)');
		});

		Schema::create('arpa_choeur', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('nom', 'text')
				->nullable();	
			$table->add('region', 'text')
				->nullable();	
			$table->add('adresse', 'text')
				->nullable();	
			$table->add('ville', 'text')
				->nullable();	
			$table->add('code_postal', 'text')
				->nullable();	
			$table->add('telephone', 'text')
				->nullable();	
			$table->add('mobile', 'text')
				->nullable();	
			$table->add('email', 'text')
				->nullable();	
			$table->add('site_web', 'text')
				->nullable();	
			$table->add('lieu_repetition_adresse', 'text')
				->nullable();	
			$table->add('lieu_repetition_ville', 'text')
				->nullable();	
			$table->add('lieu_repetition_code_postal', 'text')
				->nullable();	
			$table->add('repetitions_horaires', 'text')
				->nullable();	
			$table->add('style_musical', 'text')
				->nullable();	
			$table->add('responsable_adresse', 'text')
				->nullable();	
			$table->add('responsable_code_postal', 'text')
				->nullable();	
			$table->add('responsable_ville', 'text')
				->nullable();	
			$table->add('responsable_nom', 'text')
				->nullable();	
			$table->add('responsable_prenom', 'text')
				->nullable();	
			$table->add('responsable_telephone', 'text')
				->nullable();	
			$table->add('responsable_mobile', 'text')
				->nullable();	
			$table->add('responsable_email', 'text')
				->nullable();	
			$table->add('conditions_admission', 'text')
				->nullable();	
			$table->add('type_choeurs', 'text')
				->nullable();	
			$table->add('slug', 'text')
				->nullable();	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('arpa_document', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('titre', 'text')
				->nullable();	
			$table->add('description', 'text')
				->nullable();	
			$table->add('position', 'int(11)');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');	
			$table->add('filename_document', 'text')
				->nullable();
		});

		Schema::create('arpa_foo', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('date_naissance', 'text');	
			$table->add('mot_de_passe', 'text');	
			$table->add('email', 'text');	
			$table->add('slug', 'text');	
			$table->add('position', 'int(11)');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');	
			$table->add('filename_image', 'text');
		});

		Schema::create('arpa_formation', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('titre', 'text')
				->nullable();	
			$table->add('date', 'text')
				->nullable();	
			$table->add('lieu', 'text')
				->nullable();	
			$table->add('introduction', 'text')
				->nullable();	
			$table->add('contenu', 'text')
				->nullable();	
			$table->add('meta_title', 'text')
				->nullable();	
			$table->add('meta_description', 'text')
				->nullable();	
			$table->add('meta_keywords', 'text')
				->nullable();	
			$table->add('slug', 'text')
				->nullable();	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');	
			$table->add('filename_image', 'text')
				->nullable();
		});

		Schema::create('arpa_inscrit', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('email', 'text')
				->nullable();	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('arpa_page', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('title', 'varchar(255)');	
			$table->add('name', 'text')
				->nullable();	
			$table->add('content', 'text')
				->nullable();	
			$table->add('position', 'int(11)');	
			$table->add('meta_title', 'text')
				->nullable();	
			$table->add('meta_description', 'text')
				->nullable();	
			$table->add('meta_keywords', 'text')
				->nullable();	
			$table->add('slug', 'text')
				->nullable();	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('arpa_preferences', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('name', 'text')
				->nullable();	
			$table->add('email', 'varchar(255)')
				->nullable();	
			$table->add('adresse', 'varchar(255)')
				->nullable();	
			$table->add('telephone', 'varchar(255)')
				->nullable();	
			$table->add('head_script', 'text')
				->nullable();
		});

		Schema::create('arpa_professeur', function($table) {	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('nom', 'text')
				->nullable();	
			$table->add('prenoms', 'text')
				->nullable();	
			$table->add('region', 'text')
				->nullable();	
			$table->add('adresse', 'text')
				->nullable();	
			$table->add('ville', 'text')
				->nullable();	
			$table->add('code_postal', 'text')
				->nullable();	
			$table->add('telephone', 'text')
				->nullable();	
			$table->add('email', 'text')
				->nullable();	
			$table->add('site_web', 'text')
				->nullable();	
			$table->add('cours_particuliers', 'text')
				->nullable();	
			$table->add('type_choeurs', 'text')
				->nullable();	
			$table->add('informations_complementaires', 'varchar(600)');	
			$table->add('slug', 'text')
				->nullable();	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');
		});

		Schema::create('arpa_test', function($table) {	
			$table->add('date', 'text');	
			$table->add('lieu', 'text');	
			$table->add('titre', 'text');	
			$table->add('introduction', 'text');	
			$table->add('contenu', 'text');	
			$table->add('id', 'int(11)');	
			$table->add('slug', 'text');	
			$table->add('created_at', 'datetime');	
			$table->add('updated_at', 'datetime');	
			$table->add('filename_image', 'text');	
			$table->add('actualite_id', 'int(11)');
		});
	}
}