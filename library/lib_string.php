<?PHP

// Универсальная библиотека для частой работы со строками


function LibString_GetDateNice($dateText)
{
	$monthNames = array('Неизвестно', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');

	$newDate = new DateTime($dateText);
	$monthIndex = $newDate->format("n");

	$day = $newDate->format("j ");
	$month = $monthNames[$monthIndex] . ', ';
	$year = $newDate->format("Y г.");

	$time = $newDate->format(", H:s");

	return $day . $month . $year . $time;
}


// Преобразует строку с запятыми в список тэгов
function LibString_TextToTagList($text, $textDevider, $tagsDevider, $url, $tagUrlName = 'tag')
{
	// очищаем ссылку от тега, во избежании дублирования
	$url = LibUrl_ClearFromParameter($url, $tagUrlName);

	$tagList = explode(',', $text);
	$count = count($tagList);
	for ($i = 0; $i < $count; $i++) {
		$tagList[$i] = trim($tagList[$i]);
		if ($tagList[$i] === '') {
			continue;
		}
		$tagUrlValue = mb_strtolower($tagList[$i], 'UTF-8');
		$tagUrlValue = str_replace(' ', '%20', $tagUrlValue);

		$tagLabel = LibString_FirstUppercase($tagList[$i]);

		$urlRequest = LibUrl_AddParameter($url, $tagUrlName, $tagUrlValue);

		$tagList[$i] = '<a href="'.$urlRequest.'" title="Отфильтровать по этому критерию">' . $tagLabel . '</a>';
	}
	$result = implode($tagsDevider, $tagList);
	return $result;
}


// Изменяет регистр: первый символ с большой буквы, остальные с маленькой
function LibString_FirstUppercase($text)
{
	$first = mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8');
	$last = mb_substr($text, 1);
	return $first . $last;
}


// Возвращает правильное обозначение числа в соответствии с правилами русского языка
function LibString_GetQuantRemark($number, $textOne, $textMany, $textManyAlt)
{
	$number = trim($number);
	if ($number == '') {
		$number = '0';
	}

	$last = strlen($number) - 1;
	$lastDigit = $number[$last];
	if (($last - 1) >= 0) {
		$lastTwoDigits = $number[$last-1] . $lastDigit;
	} else {
		$lastTwoDigits = '0';
	}
	$lastDigit = intval($lastDigit);
	$lastTwoDigits = intval($lastTwoDigits);

	// case: 11, 12, 13
	if (($lastTwoDigits == 11) || ($lastTwoDigits == 12) || ($lastTwoDigits == 13) || ($lastTwoDigits == 14)) {
		return $textMany;
	}
	// case: 1, ... 11, 21, ... 111, 221, 331
	elseif ($lastDigit == 1) {
		return $textOne;
	}
	// case: 2, 3, 4, ... 12, 13, 14, ... X2, X3, X4
	elseif (($lastDigit == 2) || ($lastDigit == 3) || ($lastDigit == 4)) {
		return $textManyAlt;
	}
	// other: 0, 5, 6, 7, 8, 9, 10, ...
	else {
		return $textMany;
	}
}


// Возвращает вхождение $subtext в $text или возвращает -1 если не найдено
function LibString_Pos($subtext, $text, $searchFrom = 0)
{
	if (($text == '') || ($subtext == '')) {
		return -1;
	}
	if (($searchFrom < 0) || ($searchFrom > mb_strlen($text, 'UTF-8')-1)) {
		return -1;
	}
	$pos = mb_strpos($text, $subtext, $searchFrom, 'UTF-8');
	if ($pos === false) $pos = -1;
	return $pos;
}