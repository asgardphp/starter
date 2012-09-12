<?php
class diff_1 {
	public static function up() {
		Schema::create('administrator', function($table) {	
			$table->add('username', 'varchar(100)');	
			$table->add('password', 'varchar(100)');	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('created_at', 'date');	
			$table->add('updated_at', 'date');
		});

		Schema::create('value', function($table) {	
			$table->add('key', 'text');	
			$table->add('value', 'text');	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('created_at', 'date');	
			$table->add('updated_at', 'date');
		});

		Schema::create('morceau_recette', function($table) {	
			$table->add('recette_id', 'int(11)');	
			$table->add('morceau_id', 'int(11)');
		});

		Schema::create('morceau', function($table) {	
			$table->add('nom', 'text');	
			$table->add('animal', 'text');	
			$table->add('description', 'text');	
			$table->add('position', 'int(11)');	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('created_at', 'date');	
			$table->add('updated_at', 'date');
		});

		Schema::create('recette', function($table) {	
			$table->add('titre', 'text');	
			$table->add('complexite', 'text');	
			$table->add('budget', 'text');	
			$table->add('temps_de_preparation', 'text');	
			$table->add('temps_de_cuisson', 'text');	
			$table->add('type_de_plat', 'text');	
			$table->add('ingredients', 'text');	
			$table->add('preparation', 'text');	
			$table->add('animal', 'text');	
			$table->add('saison', 'text');	
			$table->add('photo_small', 'varchar(255)');	
			$table->add('photo_big', 'varchar(255)');	
			$table->add('position', 'int(11)');	
			$table->add('id', 'int(11)')
				->autoincrement()
				->primary();	
			$table->add('created_at', 'date');	
			$table->add('updated_at', 'date');
		});
	}
	
	public static function down() {
		
	}
}