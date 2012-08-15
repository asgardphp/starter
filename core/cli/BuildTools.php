<?php
class BuildTools {
	public static function outputPHP($value) {
		return var_export($value, true);
		//~ if($value===true)
			//~ return 'true';
		//~ elseif($value===false)
			//~ return 'false';
		//~ elseif(is_array($value))
			//~ return '?';
		//~ else
			//~ return "'".str_replace("'", "\'", $value)."'";
	}
}