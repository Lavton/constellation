<?php
// получаем информацию с danetka.ru

include $_SERVER['DOCUMENT_ROOT'] . '/standart/php/simple_html_dom.php';
if (isset($_POST["action"]) && !empty($_POST["action"])) {
	//Checks if action value exists
	$action = $_POST["action"];
	switch ($action) {
		case "all_pages":all_pages();
			break;
	}
}

// список страниц с ссылками
function all_pages(){
	$base = "http://www.danetka.ru/";
	$html = file_get_html('http://www.danetka.ru/cgi-bin/resolved.pl');
	$pageUrls = array();
	array_push($pageUrls, Array("url" => 'http://www.danetka.ru/cgi-bin/resolved.pl', "name" => "current"));
	foreach($html->find('td.index', 0)->find('a') as $element) 
		array_push($pageUrls, Array("url" => $base.($element->href), "name" => $element->plaintext));
	echo json_encode($pageUrls);
}

?>