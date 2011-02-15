<?php
/* Requires this table:
CREATE TABLE translation (
	id int NOT NULL AUTO_INCREMENT, -- optional
	language_id varchar(5) NOT NULL,
	idf varchar(100) NOT NULL COLLATE utf8_bin,
	translation text NOT NULL,
	UNIQUE (language_id, idf),
	PRIMARY KEY (id)
);
*/

/** Translate all table and field comments, enum and set values from the translation table (inserts new translations)
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerTranslation {
	
	function _translate($s) {
		static $translations, $lang;
		if (!isset($lang)) {
			$lang = get_lang();
		}
		if ($s == "" || $lang == "en") {
			return $s;
		}
		if (!isset($translations)) {
			$translations = get_key_vals("SELECT idf, translation FROM translation WHERE language_id = " . q($lang));
		}
		$idf = preg_replace('~^(.{100}).*~su', '\\1', $s);
		$return = &$translations[$idf];
		if (!isset($return)) {
			$return = $s;
			$connection = connection();
			$connection->query("INSERT INTO translation (language_id, idf, translation) VALUES (" . q($lang) . ", " . q($idf) . ", " . q($s) . ")");
		}
		return $return;
	}
	
	function tableName(&$tableStatus) {
		$tableStatus["Comment"] = $this->_translate($tableStatus["Comment"]);
	}
	
	function fieldName(&$field, $order = 0) {
		$field["comment"] = $this->_translate($field["comment"]);
	}
	
	function editVal(&$val, $field) {
		if ($field["type"] == "enum") {
			$val = $this->_translate($val);
		}
	}
	
}