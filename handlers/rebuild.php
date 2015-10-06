<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/own/passwords.php';
$link = mysqli_connect(
	Passwords::$db_host, /* Хост, к которому мы подключаемся */
	Passwords::$db_user, /* Имя пользователя */
	Passwords::$db_pass, /* Используемый пароль */
	Passwords::$db_name); /* База данных для запросов по умолчанию */

	if (!$link) {
		printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
		exit;
	}
	$link->set_charset("utf8");
/*	$query = 'DELETE FROM UsersMain WHERE 1;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	$query = 'SELECT * FROM fighters;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "id");
		}
		if (isset($line["vk_id"])) {
			array_push($values, "'" . $line["vk_id"] . "'");
			array_push($names, "uid");
		}
		if (isset($line["name"])) {
			array_push($values, "'" . $line["name"] . "'");
			array_push($names, "first_name");
		}
		if (isset($line["surname"])) {
			array_push($values, "'" . $line["surname"] . "'");
			array_push($names, "last_name");
		}
		if (isset($line["second_name"])) {
			array_push($values, "'" . $line["second_name"] . "'");
			array_push($names, "middle_name");
		}
		if (isset($line["phone"])) {
			array_push($values, "'" . $line["phone"] . "'");
			array_push($names, "phone");
		}
		if (isset($line["birthdate"])) {
			array_push($values, "'" . $line["birthdate"] . "'");
			array_push($names, "birthdate");
		}
		if (isset($line["group_of_rights"])) {
			array_push($values, "'" . $line["group_of_rights"] . "'");
			array_push($names, "group_of_rights");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO UsersMain (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');

		$names = array();
		$values = array();
		array_push($values, "'" . $line["id"] . "'");
		array_push($names, "id");
		if (isset($line["maiden_name"])) {
			array_push($values, "'" . $line["maiden_name"] . "'");
			array_push($names, "maiden_name");
		}
		if (isset($line["nickname"])) {
			array_push($values, "'" . $line["nickname"] . "'");
			array_push($names, "nickname");
		}
		if (isset($line["second_phone"])) {
			array_push($values, "'" . $line["second_phone"] . "'");
			array_push($names, "second_phone");
		}
		if (isset($line["email"])) {
			array_push($values, "'" . $line["email"] . "'");
			array_push($names, "email");
		}			
		if (isset($line["Instagram_id"])) {
			array_push($values, "'" . $line["Instagram_id"] . "'");
			array_push($names, "instagram_id");
		}
		if (isset($line["year_of_entrance"])) {
			array_push($values, "'" . $line["year_of_entrance"] . "'");
			array_push($names, "year_of_entrance");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query3 = "INSERT INTO UsersFighters (" . $names . ") VALUES (" . $values . ");";
		$rt3 = mysqli_query($link, $query3) or die('Запрос не удался: '.$query3);

	}


	$query = 'SELECT * FROM achievements;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "id");
		}
		if (isset($line["fighter_id"])) {
			array_push($values, "'" . $line["fighter_id"] . "'");
			array_push($names, "fighter");
		}
		if (isset($line["start_year"])) {
			array_push($values, "'" . $line["start_year"] . "'");
			array_push($names, "start_year");
		}
		if (isset($line["finish_year"])) {
			array_push($values, "'" . $line["finish_year"] . "'");
			array_push($names, "finish_year");
		}
		if (isset($line["achiev"])) {
			array_push($values, "'" . $line["achiev"] . "'");
			array_push($names, "achievement");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO UsersFightersAchievements (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');
	}



	$query = 'DELETE FROM EventsMain WHERE 1;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	$query = 'SELECT * FROM events;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "id");
		}
		if (isset($line["name"])) {
			array_push($values, "'" . $line["name"] . "'");
			array_push($names, "name");
		}
		if (isset($line["place"])) {
			array_push($values, "'" . $line["place"] . "'");
			array_push($names, "place");
		}
		if (isset($line["visibility"])) {
			array_push($values, "'" . $line["visibility"] . "'");
			array_push($names, "visibility");
		}
		if (isset($line["comments"])) {
			array_push($values, "'" . $line["comments"] . "'");
			array_push($names, "comments");
		}
		if (isset($line["last_updated"])) {
			array_push($values, "'" . $line["last_updated"] . "'");
			array_push($names, "last_updated");
		}
		if (isset($line["start_time"])) {
			$st = $line["start_time"];
			array_push($values, "'" . $st[0].$st[1].$st[2].$st[3].$st[4].$st[5].$st[6].$st[7].$st[8].$st[9]. "'");
			array_push($names, "start_date");

			array_push($values, "'" . $st[11].$st[12].$st[13].$st[14].$st[15].$st[16].$st[17].$st[18]. "'");
			array_push($names, "start_time");
		}

		if (isset($line["end_time"])) {
			$st = $line["end_time"];
			array_push($values, "'" . $st[0].$st[1].$st[2].$st[3].$st[4].$st[5].$st[6].$st[7].$st[8].$st[9]. "'");
			array_push($names, "finish_date");

			array_push($values, "'" . $st[11].$st[12].$st[13].$st[14].$st[15].$st[16].$st[17].$st[18]. "'");
			array_push($names, "finish_time	");
		}

		if (isset($line["parent_id"])) {
			array_push($values, "'" . $line["parent_id"] . "'");
			array_push($names, "parent_id");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO EventsMain (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');

		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "id");
		}
		if (isset($line["contact"])) {
			array_push($values, "'" . $line["contact"] . "'");
			array_push($names, "contact");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO EventsEvents (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');

		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO EventsMain (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');

		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "event");
		}
		if (isset($line["editor"])) {
			array_push($values, "'" . $line["editor"] . "'");
			array_push($names, "editor");
			$names = implode(", ", $names);
			$values = implode(", ", $values);

			$query2 = "INSERT INTO EventsEventsEditors (" . $names . ") VALUES (" . $values . ");";
			$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: '. $query2);
		}
	}


	$query = 'SELECT * FROM shifts;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		if (isset($line["time_name"])) {
			array_push($values, "'" . $line["time_name"] . "'");
			array_push($names, "name");
		}
		if (isset($line["place"])) {
			array_push($values, "'" . $line["place"] . "'");
			array_push($names, "place");
		}
		if (isset($line["visibility"])) {
			array_push($values, "'" . $line["visibility"] . "'");
			array_push($names, "visibility");
		}
		if (isset($line["comments"])) {
			array_push($values, "'" . $line["comments"] . "'");
			array_push($names, "comments");
		}
		if (isset($line["last_updated"])) {
			array_push($values, "'" . $line["last_updated"] . "'");
			array_push($names, "last_updated");
		}
		if (isset($line["start_date"])) {
			array_push($values, "'" . $line["start_date"] . "'");
			array_push($names, "start_date");
		}

		if (isset($line["finish_date"])) {
			array_push($values, "'" . $line["finish_date"] . "'");
			array_push($names, "finish_date");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO EventsMain (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');

	}
	$query = "SELECT id FROM EventsMain WHERE (finish_date-start_date > 4);"
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		array_push($values, "'" . $line["id"] . "'");
		array_push($names, "id");
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO EventsShifts (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');
	}
	$query = 'SELECT EM.id AS id FROM EventsMain AS EM JOIN shifts AS S ON (EM.place=S.place AND EM.start_date=S.start_date) JOIN detachments AS D ON (D.shift_id=S.id AND D.ranking IS NULL) GROUP BY EM.id;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "shift");
		}
		array_push($values, "'" . "1" . "'");
		array_push($names, "show_it");
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO EventsShiftsRanking (" . $names . ") VALUES (" . $values . ")";
		echo $query2."<br>";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: '.$query2);
	}
*/

	$query = 'SELECT EM.id AS id, S.id AS sid  FROM EventsMain AS EM JOIN shifts AS S ON (EM.place=S.place AND EM.start_date=S.start_date) JOIN detachments AS D ON (D.shift_id=S.id AND D.ranking IS NULL) GROUP BY EM.id;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$query2 = 'SELECT D.people AS people, D.children_num as children_num, D.children_dis as children_discription, ESR.id AS srank FROM detachments AS D, EventsShiftsRanking AS ESR WHERE (ranking IS NULL AND shift_id='.$line['sid'].' AND shift='.$line['id'].');';
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');

		while ($line2 = mysqli_fetch_array($rt2, MYSQL_ASSOC)) {
			$names = array();
			$values = array();
			if (isset($line2["srank"])) {
				array_push($values, "'" . $line2["srank"] . "'");
				array_push($names, "ranking");
			}
			if (isset($line2["children_num"])) {
				array_push($values, "'" . $line2["children_num"] . "'");
				array_push($names, "children_num");
			}
			if (isset($line2["children_discription"])) {
				array_push($values, "'" . $line2["children_discription"] . "'");
				array_push($names, "children_discription");
			}

			$names = implode(", ", $names);
			$values = implode(", ", $values);
			$people = explode("$",$line2["people"]);

			$query3 = "INSERT INTO EventsShiftsDetachments (" . $names . ") VALUES (" . $values . ")";
			// $rt3 = mysqli_query($link, $query3) or die('Запрос не удался: ');
			$link->query($query3);
			$lst_id = $link->insert_id;

			foreach ($people as $key => $value) {
				$names2 = array();
				$values2 = array();
				array_push($values2, "'" . $lst_id . "'");
				array_push($names2, "detachment");
				$que="SELECT COUNT(1) AS Cid, id FROM UsersMain WHERE uid=\"".$value."\";";
				$rue = mysqli_query($link, $que) or die('Запрос не удался: ');
				$line5=mysqli_fetch_array($rue, MYSQL_ASSOC);




				if ($line5["Cid"]*1 >= 1) {
					array_push($values2, "'" . $line5["id"] . "'");
					array_push($names2, "user");
				} else {
					array_push($values2, "'" . $value . "'");
					array_push($names2, "name");					
				}

				echo $line5;

				$names2 = implode(", ", $names2);
				$values2 = implode(", ", $values2);

				$query4 = "INSERT INTO EventsShiftsDetachmentsPeople (" . $names2 . ") VALUES (" . $values2 . ")";
				$rt4 = mysqli_query($link, $query4) or die('Запрос не удался: '.$query4);
			}
		}
	}

?>