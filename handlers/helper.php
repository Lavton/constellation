<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/passwords.php';

// упрощает вставку
function inserter($link, $table, $data, $needId=False, $onDubl=False) {
	$names = array();
	$values = array();

	foreach ($data as $key => $value) {
		array_push($names, $key);
		array_push($values, "'" . $value . "'");
	}
	foreach ($values as $key => $value) {
		if ($value == "''") {
			$values[$key] = "NULL";
		}
	}
	$conc = array();
	if ($onDubl) {
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
	}
	$conc = implode(", ", $conc);

	$names = implode(", ", $names);
	$values = implode(", ", $values);
	$query = "";
	if (!$onDubl) {
		// обычная вставка
		$query = "INSERT INTO ".$table." (" . $names . ") VALUES (" . $values . ");";
	} else {
		// вставка, но при наличии ключей - обновление
		$query = "INSERT INTO ".$table." (" . $names . ") VALUES (" . $values . ") 
		ON DUPLICATE KEY UPDATE ".$conc.";";
	}
	$link->query($query);
	$result = array();
	$result["result"] = "Success";
	$result["qw"] = $query;
	if ($needId) {
		$result["id"] = $link->insert_id;
	}
	return $result;
}

// упрощает редактирование
function updater($link, $table, $data, $is_by_id=True, $condition="") {
	$names = array();
	$values = array();

	foreach ($data as $key => $value) {
		array_push($names, $key);
		array_push($values, "'" . $value . "'");
	}
	foreach ($values as $key => $value) {
		if ($value == "''") {
			$values[$key] = "NULL";
		}
	}
	$conc = array();
	foreach ($names as $key => $value) {
		array_push($conc, "" . $value . "=" . $values[$key]);
	}
	$conc = implode(", ", $conc);
	$query = "";
	if ($is_by_id) {
		$query = "UPDATE ".$table." SET " . $conc . " WHERE id='" . $data['id'] . "';";
	} else {
		$query = "UPDATE ".$table." SET " . $conc . " WHERE (" . $condition . ");";
	}
	$rt = mysqli_query($link, $query) or die('Запрос не удался1: '.$query);
	$result["result"] = "Success";
	return $result;
}

// упрощает удаление. Для сайта особенно, т.к. там не поддерживаются FK
function deleter($link, $table, $condition="") {
	if (!Passwords::$is_local) {
		$DB_Fk = json_decode(file_get_contents("DB_Foreign_keys.json"),true);
		$result["table"] = $table;
		// для всех зависимостей
		if (isset($DB_Fk[$table])) {
			foreach ($DB_Fk[$table] as $key => $value) {
				// ищем строки, которые подвергнуться редактированию
				$query = "SELECT ".$value['ref']." AS ref_value FROM ".$table." WHERE ".$condition;
				$rt = mysqli_query($link, $query) or die('Запрос не удался: '. $query);
				while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
					// если каскадно - удаляем
					if ($value["del"] == "cascade") {
						deleter($link, $value["name"], "".$value["fk"]."='".$line["ref_value"]."'");
					// иначе - выставляем на ноль.
					} else {
						updater($link, $value["name"], array($value["fk"] => "NULL"), False, "".$value["fk"]."='".$line["ref_value"]."'");
					}
				}
			}
		}
	}

	$query = "DELETE FROM ".$table." WHERE (" . $condition . ");";
	$rt = mysqli_query($link, $query) or die('Запрос не удался3: '.$query);
	$result["result"] = "Success";
	return $result;
}
?>