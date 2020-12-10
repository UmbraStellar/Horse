<?PHP

// Универсальная библиотека для работы с адресами ссылок


// Возвращает значение параметра из адресной ссылки
function LibUrl_GetParameter(string $parameter)
{
	if (!isset($_GET[$parameter])) {
		return '';
	}
	return htmlspecialchars(strip_tags($_GET[$parameter]), ENT_NOQUOTES);
}


// Возвращает значение параметра из адресной ссылки в нижнем регистре
function LibUrl_GetParameterLow(string $parameter = '')
{
	return mb_strtolower(LibUrl_GetParameter($parameter));
}


// Очищает адрес ссылки от параметра и его значения, если он найден
function LibUrl_ClearFromParameter($urlAddress, $parameterName = '', $fullMatch = true)
{
	$urlAddress = trim($urlAddress);
	$parameterName = trim($parameterName);

	if (($urlAddress == '') || ($parameterName == '')) {
		return $urlAddress;
	}
	
	// если полное соответствие, добавляем знак равно
	if ($fullMatch) {
		$parameterName .= '=';
	}
	$urlAddress = str_replace('?', '&', $urlAddress);

	// break urlAddress on parameters
	$urlParts = explode('&', $urlAddress);

	$urlPartsCleared = array();
	$urlPartsCleared[] = $urlParts[0];
	
	// clear all entrances
	$count = count($urlParts);
	for ($i = 1; $i < $count; $i++) {
		if (LibString_Pos($parameterName, $urlParts[$i]) < 0) {
			$urlPartsCleared[] = $urlParts[$i];
		}
	}
	$urlParts = $urlPartsCleared;
	$count = count($urlPartsCleared);

	// assembling url parts to the final

	if ($count == 0) {
		return '';
	}
	elseif ($count == 1) {
		return $urlPartsCleared[0];
	}
	elseif ($count == 2) {
		return $urlPartsCleared[0] . '?' . $urlPartsCleared[1];
	}
	else {
		$urlAddress = $urlPartsCleared[0] . '?' . $urlPartsCleared[1];
		for ($i = 2; $i < $count; $i++) {
			$urlAddress = $urlAddress . '&' . $urlPartsCleared[$i];
		}
		return $urlAddress;
	}
}


// Возвращает следующий валидный разделитель параметров в адресной строке
function LibUrl_GetParamDelimiter($urlAddress = '')
{
	if (LibString_Pos('?', $urlAddress) >= 0) {
		return '&';
	} else {
		return '?';
	}
}


// Добавляет в адрес ссылки новый параметр
function LibUrl_AddParameter($urlAddress = '', $parameterName = '', $parameterValue = '')
{
	$parameterFull = $parameterName . '=' . $parameterValue;
	if ($parameterFull == '') return $urlAddress;
	
	return $urlAddress . LibUrl_GetParamDelimiter($urlAddress) . $parameterFull;
}