<?php
if (!defined('__path__')) {
	define('__path__', isset($__path__) ? $__path__ : "../");
}
require_once __path__ . 'app/app.php';
/**
 *  navbar operations
 */
class Migration extends App
{

	/*navbar tables to create*/
	public $tables;

	/**
	 *   migrating tables
	 */
	public function migrate($delete = true)
	{
		/*no empty tables*/
		if (count($this->tables) === 0) {
			return "there are no tables to create";
		}

		// creting table multi query
		$sql = "";
		foreach ($this->tables as $table => $cols) {
			/* check_if_is_link and create a temporary controler */
			$sql .= ("CREATE TABLE IF NOT EXISTS " . $table . "(" . implode(',', $cols) . "); ");
		}

		/*perform the query*/
		$this->use_database();
		if (!$this->database->multi_query($sql)) {
			$this->release_database();
			return ("failure" . $this->database->error);
		} else {
			$pages = 0;
			foreach ($this->insertData as $key => $data) {
				/* check_if_is_link and create a temporary controler */
				if (strstr($data['section_id'], 'nav_link_') !== false) {
					/*view setup*/
					if (!is_file(($file = '../views/' . $data["section_title"] . '.php'))) {
						$cont = str_replace("require_once '../controlers/homeControler.php';", "require_once '../controlers/{$data['section_title']}Controler.php';",  file_get_contents(__path__ . "app/extensions/new_file.txt"));
						file_put_contents($file, $cont);
					}
					/*controler setup*/
					if (!is_file(($file = '../controlers/' . $data["section_title"] . 'Controler.php'))) {
						$cont = str_replace('xxxx', "lentec_" . preg_replace("/[^A-Za-z0-9_]/", "", $data["section_title"]),  file_get_contents("../app/extensions/new_controler.txt"));
						file_put_contents($file, $cont);
					}
					//htaccess setup
					if (is_file(($file = '../.htaccess'))) {
						$cont1 = str_replace('xxxx', $data["section_title"],  file_get_contents('../app/extensions/htaccess.txt'));
						$main =  file_get_contents($file);
						if (strstr($main, $cont1) === false) {
							$cont2 = str_replace($cont1, "", $main);
							$arr =  explode('Options -Indexes', $cont2);
							$new_cont = $arr[0];
							$error_link = scheme . $_SERVER['SERVER_NAME'] . base_path . "home/";
							$new_cont .= ($cont1 . "\n\n" . "Options -Indexes \nErrorDocument 404 " . $error_link . " \nErrorDocument 403 " . $error_link);
							file_put_contents($file, str_replace("\\n\\n\\n", "\\n\\n", $new_cont));
						}
					}

					/*migration setup*/
					if (!is_file(($file = '../migrations/' . $data["section_title"] . 'Migration.php'))) {
						$cont = str_replace("xxxx", "lentec_" . ($clean = preg_replace("/[^A-Za-z0-9_]/", " ", $data['section_title'])),  file_get_contents("../app/extensions/new_migration.txt"));
						$cont = str_replace("zzzz", $clean,  $cont);
						file_put_contents($file, $cont);
						echo $link = scheme . $_SERVER['SERVER_NAME'] . base_path . "migrations/" . $data["section_title"] . 'Migration.php';
						file_get_contents($link);
					}
				}

				$sql .= "DROP TABLE IF EXISTS " . $table . " ; CREATE TABLE " . $table . "(" . implode(',', $cols) . "); ";
			}
			$this->release_database();
			if (isset($_SESSION[$this->activeTable])) {
				unset($_SESSION[$this->activeTable]);
			}
			return  "success";
		}
	}
}
