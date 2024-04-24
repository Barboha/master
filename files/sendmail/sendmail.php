<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('ru', 'phpmailer/language/');
$mail->IsHTML(true);

//От кого письмо
$mail->setFrom('master_chats@mail.ru', 'Работка'); // Указать нужный E-mail
//Кому отправить
$mail->addAddress('master_chats@mail.ru'); // Указать нужный E-mail
//Тема письма
$mail->Subject = 'Привет! Это тебе прилетела работка';

//Тело письма
$body = '<h1>Новая работка</h1>';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if (!empty($_POST['form'])) {
		foreach ($_POST['form'] as $key => $value) {
			if (!empty($value)) {
				// Добавляем данные в письмо
				$body .= '<p>' . htmlspecialchars($value) . '</p>';
			}
		}
	}

	$fields_to_check = [
		"kuhni",
		"krovat",
		"tumba",
		"stellazh",
		"cabinet",
		"dresser",
		"table",
		"mirrorr",
		"provod",
		"drilling",
		"socket",
		"light",
		"oborudovanie",
		"demontazh",
	];

	$field_names = [
		"kuhni" => "Кухня",
		"krovat" => "Кровать",
		"tumba" => "Тумба",
		"stellazh" => "Стеллаж",
		"cabinet" => "Шкаф",
		"dresser" => "Комод",
		"table" => "Стол",
		"mirrorr" => "Зеркало",
		"provod" => "Провод",
		"drilling" => "Сверление",
		"socket" => "Розетка",
		"light" => "Светильник",
		"oborudovanie" => "Оборудование",
		"demontazh" => "Демонтаж",
	];

	foreach ($fields_to_check as $field) {
		if (!empty($_POST[$field])) {
			// Добавляем название раздела
			$field_name = $field_names[$field];
			$body .= '<h2>' . $field_name . ':</h2>';

			// Проверяем, является ли значение ассоциативным массивом
			foreach ($_POST[$field] as $value) {
				// Если значение является строкой, добавляем его в письмо
				if (is_string($value)) {
					$data = json_decode($value, true); // Декодируем JSON строку в ассоциативный массив
					foreach ($data as $name => $val) {
						// Добавляем данные в письмо
						$body .= '<p>' . htmlspecialchars($name) . ': ' . htmlspecialchars($val) . '</p>';
					}
				} elseif (is_bool($value) && $value) {
					// Если значение логическое true, добавляем только ключ
					$body .= '<p>' . htmlspecialchars($key) . '</p>';
				} elseif (is_string($value) && empty($value)) {
					// Если значение строки пусто, пропускаем
					continue;
				}
			}
		}
	}

	// Добавляем информацию о цене, если она есть
	if (!empty($_POST['price'])) {
		$body .= '<h2>Итоговая цена:</h2>';
		$body .= '<p>' . htmlspecialchars($_POST['price']) . '</p>';
	}
}


/*
	//Прикрепить файл
	if (!empty($_FILES['image']['tmp_name'])) {
		//путь загрузки файла
		$filePath = __DIR__ . "/files/sendmail/attachments/" . $_FILES['image']['name']; 
		//грузим файл
		if (copy($_FILES['image']['tmp_name'], $filePath)){
			$fileAttach = $filePath;
			$body.='<p><strong>Фото в приложении</strong>';
			$mail->addAttachment($fileAttach);
		}
	}
*/

$mail->Body = $body;

//Отправляем
if (!$mail->send()) {
	$message = 'Ошибка';
} else {
	$message = 'Данные отправлены!';
}

$response = ['message' => $message];

header('Content-type: application/json');
echo json_encode($response);
