<?php
class EmailProperty extends TextProperty {
	public function getSQLType() {
		return 'varchar(250)';
	}
}