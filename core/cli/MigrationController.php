<?php
#todo delete old model tables
namespace Coxis\Core\Cli;

class MigrationController extends CLIController {
	private static function tableSchema($table) {
		$structure = array();
		try{
			$res = DB::query('Describe '.$table)->all();
		} catch(\Exception $e) {
			return null;
		}
		foreach($res as $one) {
			$col = array();
			$col['type'] = $one['Type'];
			$col['default'] = $one['Default'];
			$col['nullable'] = $one['Null'] == 'YES';
			$col['key'] = $one['Key'];
			$col['auto_increment'] = strpos($one['Extra'], 'auto_increment') !== false;
			$struc[$one['Field']] = $col;
		}
		
		return $struc;
	}

	public function automigrateAction($request) {
		$this->diffAction($request);
		$this->migrateAction($request);
	}

	public function migrateAction($request) {
		echo 'Migrating...'."\n";
		$migrations = static::todo();
		//~ d($migrations);
		//~ d('TODO');
		
		//~ d($migrations, $current);
		if(!sizeof($migrations))
			return;
		
		foreach($migrations as $migration)
			$last = static::migrate($migration);
			
		file_put_contents('migrations/migrate_version', $last);
	}
	
	public static function migrate($migration) {
		preg_match('/([0-9]+)_([^.]+)/', $migration, $matches);
		$version = $matches[1];
		$name = $matches[2];
		$class = $name.'_'.$version;
		include($migration);
		$class::up();
		echo 'Running '.$class."\n";
		return $version;
	}
	
	public static function uptodate() {
		$migrations = static::todo();
				
		if(sizeof($migrations) > 0)
			return false;
		return true;
	}
	
	public static function current() {
		try {
		return file_get_contents('migrations/migrate_version');
		} catch(\ErrorException $e) {
			return 0;
		}
	}
	
	public static function todo() {
		$migrations = array();
		$files = glob('migrations/*.php');
		foreach($files as $file) {
			preg_match('/\/([0-9]+)_/', $file, $matches);
			//~ d($matches);
			$migrations[$matches[1]] = $file;
		}
		ksort($migrations);
		$current = static::current();
		foreach($migrations as $k=>$v)
			if($k <= $current)
				unset($migrations[$k]);
				
		return $migrations;
	}

	public function diffAction($request) {
		#todo check migration version
		if(!static::uptodate())
			die('You must run all migrations before using diff.');
			
		FileManager::mkdir('migrations');
		echo 'Running diff..'."\n";
	
		$bundles = BundlesManager::getBundles();
		
		foreach($bundles as $bundle) {
			foreach(glob($bundle.'/models/*.php') as $model) {
				include($model);
			}
		}
		
		$newSchemas = array();
		$oldSchemas = array();
		foreach(get_declared_classes() as $class) {
			//~ if($class instanceof Coxis\Core\ORM\ModelORM) {
			if(is_subclass_of($class, 'Coxis\Core\ORM\ModelORM')) {
				if($class::getModelName() == 'modelorm')
					continue;
				$class::_autoload();
				
				$schema = array();
				
				$oldSchemas[$class] = static::tableSchema($class::getTable());
				
				foreach($class::$properties as $name=>$prop) {
					if(!isset($prop['orm']))
						$prop['orm'] = array();
					//~ if($name == 'id') {
						//~ $prop['orm']['type'] = 'int(11)';
						//~ $prop['orm']['auto_increment'] = true;
						//~ $prop['orm']['key'] = 'PRI';
						//~ $prop['orm']['nullable'] = false;
					//~ }
					if(isset($prop['i18n']) && $prop['i18n'])
						continue;
					if(!isset($prop['orm']['type'])) {
						#match type
						#and length
						switch($prop['type']) {
							case 'integer':
								if(isset($prop['length']))
									$prop['orm']['type'] = 'int('.$prop['lenght'].')';
								else
									$prop['orm']['type'] = 'int(11)';
								break;
							case 'text':
								if(isset($prop['length']))
									$prop['orm']['type'] = 'varchar('.$prop['length'].')';
								else
									$prop['orm']['type'] = 'text';
								break;
							case 'date':
								$prop['orm']['type'] = 'datetime';
								break;
							case 'datetime':
								$prop['orm']['type'] = 'datetime';
								break;
							case 'email':
								$prop['orm']['type'] = 'varchar(255)';
								break;
							default:
								die('Cannot convert '.$prop['type'].' type');
						}
					}
					if(!isset($prop['orm']['default']))
						$prop['orm']['default'] = false;
					if(!isset($prop['orm']['nullable']))
						$prop['orm']['nullable'] = false;
					if(!isset($prop['orm']['key']))
						$prop['orm']['key'] = '';
					if(!isset($prop['orm']['auto_increment']))
						$prop['orm']['auto_increment'] = false;
					$schema[$name] = $prop['orm'];
				}
					
				$newSchemas[$class] = $schema;
			}
		}
		
		$oldSchemas = array_filter($oldSchemas);
			
		$up = static::diff($newSchemas, $oldSchemas);
		//~ d($up);
		$down = static::diff($oldSchemas, $newSchemas);
		//~ d($down);
		
		static::addMigration($up, $down);
		//~ die('TODO');
	}
	
	private static function diff($newSchemas, $oldSchemas) {
		$migrations = array();
		$migration = '';
		foreach($newSchemas as $class=>$schema) {
			$table = $class::getTable();
			if(!in_array($class, array_keys($oldSchemas))) {
				$migration = static::buildTableFor($class, $newSchemas[$class]);
				$migrations[] = $migration;
				continue;
			}
			$tableSchema = $oldSchemas[$class];
			$schema = $newSchemas[$class];
			$oldcols = array_diff(array_keys($tableSchema), array_keys($schema));
			$newcols = array_diff(array_keys($schema), array_keys($tableSchema));
			$colsmigration = '';
			foreach(array_keys($schema) as $col) {
				if(!in_array($col, array_keys($tableSchema))) {
					$colsmigration .=  static::buildColumnFor($table, $col, $schema[$col]);
				}
				else {
					$diff = array_diff_assoc($schema[$col], $tableSchema[$col]);
					if($diff)
						$colsmigration .=  static::updateColumn($table, $col, $diff);
				}
			}
			foreach(array_keys($tableSchema) as $col) {
				if(!in_array($col, array_keys($schema))) {
					$colsmigration .=  static::dropColumn($col);
				}
			}
			if($colsmigration) {
				$migration = "Schema::table('$table', function(\$table) {".$colsmigration."\n});";
				$migrations[] = $migration;
			}
		}
		return $migrations;
	}
	
	private static function addMigration($up, $down) {
		if(!$up)
			return;
		if(!is_array($up))
			$up = array($up);
		foreach($up as $k=>$v)
			$up[$k] = static::tabs($v, 2);
		if(!is_array($down))
			$down = array($down);
		foreach($down as $k=>$v)
			$down[$k] = static::tabs($v, 2);
			
		$filename = 'diff';
		$i = static::current()+1;
			
		$migration = '<?php
class '.$filename.'_'.$i.' {
	public static function up() {
		'.implode("\n\n\t\t", $up).'
	}
	
	public static function down() {
		'.implode("\n\n\t\t", $down)."
	}
}";
		file_put_contents('migrations/'.$i.'_'.$filename.'.php', $migration);
		echo 'New migration: '.$i.'_'.$filename;
	}
	
	private static function tabs($str, $tabs) {
		//~ return $str;
		return implode("\n".str_repeat("\t", $tabs), explode("\n", $str));
	}
	
	private static function dropColumn($col) {
		$migration = "\n\t\$table->drop('$col');";
		return $migration;
	}
	
	//~ private static function _isset($arr, $key) {
		//~ try {
			//~ $arr[$key];
			//~ return true;
		//~ } catch(\ErrorException $e) {
			//~ return false;
		//~ }
	//~ }
	
	private static function updateColumn($table, $col, $diff) {
		//~ d($definition);
		$migration = "\n\t\$table->col('$col')";
		if(isset($diff['type']))
			$migration .= "\n		->type('$diff[type]')";
		if(isset($diff['nullable']))
			if($diff['nullable'])
				$migration .= "\n		->nullable()";
			else
				$migration .= "\n		->NotNullable()";
		if(isset($diff['auto_increment']))
			if($diff['auto_increment'])
				$migration .= "\n		->autoincrement()";
			else
				$migration .= "\n		->notAutoincrement()";
		if(isset($diff['default'])) {
		//~ if(static::_isset($diff, 'default')) {
			if($diff['default'] === false)
				$migration .= "\n		->def(false)";
			else
				$migration .= "\n		->def('$diff[default]')";
		}
		if(isset($diff['key'])) {
		//~ if(static::_isset($diff, 'key')) {
			if($diff['key']=='PRI')
				$migration .= "\n		->primary()";
			elseif($diff['key']=='UNI')
				$migration .= "\n		->unique()";
			elseif($diff['key']=='MUL')
				$migration .= "\n		->index()";
			else
				$migration .= "\n		->dropIndex()";
		}
		$migration .= ";";
		//~ if($col == 'lieu')
			//~ d($diff, $migration);
		
		return $migration;
	}
	
	private static function buildColumnFor($table, $col, $definition) {
		//~ d($migration);
		$migration = '';
		//~ d($definition);
		$migration = "\n\t\$table->add('$col', '$definition[type]')";
		if($definition['nullable'])
			$migration .= "\n		->nullable()";
		if($definition['auto_increment'])
			$migration .= "\n		->autoincrement()";
		if($definition['default'])
			$migration .= "\n		->def('$definition[default]')";
		if($definition['key']=='PRI')
			$migration .= "\n		->primary()";
		if($definition['key']=='UNI')
			$migration .= "\n		->unique()";
		if($definition['key']=='MUL')
			$migration .= "\n		->index()";
		$migration .= ";";
		
		return $migration;
	}
	
	private static function buildTableFor($class, $definition) {
		//~ d($class);
		$table = $class::getTable();
		//~ d(_ENV_);
		//~ d($definition);
		
		$migration = "Schema::create('$table', function(".'$table'.") {";
		//~ $migration .= "\n";
		//~ d($definition);
		foreach($definition as $col=>$col_definition) {
			//~ $migration .= "\t".'$table->add'."('$col', '$col_definition[type]');";
			$migration .= "\t".static::buildColumnFor($table, $col, $col_definition);
			//~ $migration .= "\n";
		}
		$migration .= "\n});";
		//~ d($migration);
		
		return $migration;
	}
}