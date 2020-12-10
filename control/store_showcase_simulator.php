<?PHP

// Создаём и заполняем наборы данных для тестового рандомайзера
$horseDataSet = new \simulator\HorseDataSet($root);

// Смотрим все элементы каталога

for ($cardId = 1; $cardId <= $store->itemsOnPage; $cardId++)
{
	// Уничтожаем данные из предыдущей карточки
	unset($storeCard);

	// Проверяем параметр рандомизации
	if ($urlStore->paramRandomize == '') {
		// Читаем карточку из файловой базы, если параметр рандомизации не указан
		$storeCard = new \simulator\HorseFileReader($cardId);
	} else {
		// Генерируем карточку в зависимости от параметра рандомизации
		$storeCard = $horseDataSet->getRandomCard($cardId, $urlStore->paramRandomize);;
	}

	// Считываем статус заказа для данной карточки из истории активности клиента
	$client->getOrderStatus($cardId);
	if (($storeCard->clientId == '') && !$storeCard->orderSigned) {
		$client->setOrderStatus($cardId, false, false);
		$storeCard->setOrderStatus($client->orderSigned, $client->orderPayed, $client->id);
	} else {
		// Инициализируем статус заказа (подписан / оплачен)
		$storeCard->setOrderStatus($storeCard->orderSigned, $storeCard->orderPayed, $storeCard->clientId);
	}

	$storeCard->setUrl($urlStore->cleared);

	if ($storeCard->orderSigned && !$storeCard->orderPayed && $storeCard->correct) {
		$store->signedCount++;
	}

	if ($storeCard->orderSigned && $storeCard->orderPayed && $storeCard->correct) {
		$store->payedCount++;
	}

	if (!$storeCard->available && ((!$urlStore->paramMode == 'admin') && ($urlStore->paramTagStatus == ''))) {
		continue;
	}

	if (($urlStore->paramTagStatus == 'договор подписан') && $storeCard->orderPayed) {
		continue;
	}

	if (!$storeCard->correct && (!$urlStore->paramMode == 'admin')) {
		continue;
	}

	if ($storeCard->available) {
		$store->availableCount++;
		if ($storeCard->canRent) $store->rentCount++;
		if ($storeCard->canBuy) $store->buyCount++;
	}

	if (!$storeNavigation->dataMatchesFilter($storeCard, $urlStore)) {
		continue;
	}

	// Сброс параметров декоратора
	$cardDecorator->reset();

	// Рендеринг карточки
	include("templates/store_card.html");
	$store->requestCount++;

}

// Обновляем метрики каталога в навигатор
if ($urlStore->paramRandomize == '')  {
	include("templates/store_navigation_metrics.html");

	// Функционал взаимодействия с кнопками
	if ($store->requestCount > 0) {
		include("control/store_cards_buttons_control_common.html");

		if ($urlStore->paramMode == 'admin') {
			include("control/store_cards_buttons_control_admin.html");
		} else {
			include("control/store_cards_buttons_control_client.html");
		}
	}
}

// Вычищаем из ссылки параметр рандомизации для создания базы, если он там есть (с помощью переадресации)
if ($urlStore->paramRandomize <> '') {
	echo '<meta http-equiv="refresh" content="0;url=' . $urlStore->cleared . '">';
}

// Выводим уведомление о пустом каталоге, если не найдено ни одной карточки в каталоге
if ($store->requestCount == 0) {
	include("templates/store_cards_not_found.html");
}
