<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';

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
?>