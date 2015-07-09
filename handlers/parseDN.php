<?php
// получаем информацию с danetka.ru

include $_SERVER['DOCUMENT_ROOT'] . '/standart/php/simple_html_dom.php';
if (isset($_POST["action"]) && !empty($_POST["action"])) {
	//Checks if action value exists
	$action = $_POST["action"];
	switch ($action) {
		case "all_pages": all_pages();
			break;
		case "pages": pages($_POST["url"]);
			break;
		case "situation": situation($_POST["url"]);
			break;
	}
}

// список списка страниц с ссылками
function all_pages(){
	$base = "http://www.danetka.ru/";
	$html = file_get_html('http://www.danetka.ru/cgi-bin/resolved.pl');
	$pageUrls = array();
	array_push($pageUrls, Array("url" => 'http://www.danetka.ru/cgi-bin/resolved.pl', "name" => "current"));
	foreach($html->find('td.index', 0)->find('a') as $element) 
		array_push($pageUrls, Array("url" => $base.($element->href), "name" => $element->plaintext));
	echo json_encode($pageUrls);
}

// список страниц
function pages($url) {
  $base = "http://www.danetka.ru/";
	$html = file_get_html($url);
	$sitUrls = array();
	foreach($html->find('td.danet_info a.danet_info') as $element) 
		if (mb_convert_encoding($element->plaintext, "UTF-8", "windows-1251") == "[Архив вопросов]") {
			array_push($sitUrls, $base.($element->href));
		}
	echo json_encode($sitUrls);
}

// вопрос и ответ по "Ситуации" с её страницы
function situation($url) {
  $base = "http://www.danetka.ru/";
	$html = file_get_html($url);
	$sitAnswer = array();
	array_push($sitAnswer, mb_convert_encoding($html->find("td.danet_header", 0)->plaintext, "UTF-8", "windows-1251"));
	foreach($html->find('td.danet_text') as $element) 
		array_push($sitAnswer, mb_convert_encoding($element->plaintext, "UTF-8", "windows-1251"));
	echo json_encode($sitAnswer);
}

?>