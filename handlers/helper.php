<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/passwords.php';

// упрощает вставку
function inserter($link, $table, $data, $needId=False) {
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
	$names = implode(", ", $names);
	$values = implode(", ", $values);
	$query = "INSERT INTO ".$table." (" . $names . ") VALUES (" . $values . ");";
	$link->query($query);
	$result = array();
	$result["result"] = "Success";
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
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$result["result"] = "Success";
	return $result;
}

// упрощает удаление. Для сайта особенно, т.к. там не поддерживаются FK
function deleter($link, $table, $condition="") {
	if (!Passwords::$is_local) {
		$DB_Fk = json_decode(file_get_contents("DB_Foreign_keys.json"),true);

		// для всех зависимостей
		foreach ($DB_Fk[$table] as $key => $value) {
			// ищем строки, которые подвергнуться редактированию
			$query = "SELECT FK.".$value["fk"]. "AS getting_for_key FROM ".$value["name"]." AS FK 
			JOIN ".$table." AS TB ON TB.".$value["ref"].";";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
				// если каскадно - удаляем
				if ($value["del"] == "cascade") {
					deleter($link, $value["name"], "".$value["fk"]."=".$line["getting_for_key"]);

				// иначе - выставляем на ноль.
				} else {
					updater($link, $value["name"], array($value["fk"] => "NULL"), False, "".$value["fk"]."=".$line["getting_for_key"]);
				}
			}
		}
	}

	$query = "DELETE FROM ".$table." WHERE (" . $condition . ");";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$result["result"] = "Success";
	return $result;
}
?>