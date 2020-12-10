<?PHP

// Класс для работы с тестовой файловой базой данных активности клиента

namespace visual;

class StoreNavigation
{
	public $filterStatus;
	
	public $styleAll = '';
	public $styleRent = '';
	public $styleBuy = '';
	public $styleAdmin = '';
	public $styleSigned = '';
	public $stylePayed = '';
	
	public $queryCleaner = '';
	public $queryResult = '';
	
	public $boldFontStyle = 'style="font-weight:bold;"';
		
		
	public function defineFontStyle($urlStore)
	{		
		if ($urlStore->paramTagStatus == 'договор подписан') {
			$this->styleSigned = $this->boldFontStyle;
		}
		elseif ($urlStore->paramTagStatus == 'счёт оплачен') {
			$this->stylePayed = $this->boldFontStyle;
		}
		elseif ($urlStore->paramTagService == 'аренда') {
			$this->styleRent = $this->boldFontStyle;
		}
		elseif ($urlStore->paramTagService == 'покупка') {
			$this->styleBuy = $this->boldFontStyle;
		}
		elseif ($urlStore->paramMode == 'admin') {
			$this->styleAdmin = $this->boldFontStyle;
		}
		else {
			$this->styleAll = $this->boldFontStyle;
		}		
	}
	
	
	public function defineFilter($urlStore)
	{
		$filterStatus = $this->getFilterStatus($urlStore);

		if ($filterStatus <> '') {
			$this->queryCleaner = '<big><a href="'.$urlStore->cleared.'">Сбросить фильтр</a> &thinsp; • &thinsp;</big>';
			$this->queryResult = '<big><a id="navTag" href="' . $urlStore->full .'" ' . $this->boldFontStyle . ' >' . $filterStatus .'</a></big>';
		}		
	}
	
	
	public function getFilterStatus($urlStore)
	{
		$filterStatus = '';

		if (!empty($this->checkFilter($urlStore->paramTagGender, 'Пол лошади', $filterStatus))) {
			return $filterStatus;
		}
		elseif (!empty($this->checkFilter($urlStore->paramTagAge, 'Возрастная категория', $filterStatus))) {
			return $filterStatus;
		}
		elseif (!empty($this->checkFilter($urlStore->paramTagBreed, 'Порода лошади', $filterStatus))) {
			return $filterStatus;
		}
		elseif (!empty($this->checkFilter($urlStore->paramTagCharacter, 'Характер лошади', $filterStatus))) {
			return $filterStatus;
		}
		elseif (!empty($this->checkFilter($urlStore->paramTagHomeland, 'Место рождения', $filterStatus))) {
			return $filterStatus;
		}	
		elseif (!empty($this->checkFilter($urlStore->paramTagUsing, 'Вариант использования', $filterStatus))) {
			return $filterStatus;
		}	
		elseif (!empty($this->checkFilter($urlStore->paramTagWeight, 'Весовая категория', $filterStatus))) {
			return $filterStatus;
		}	
	}


	public function checkFilter($parameter, $title, &$filterStatus) {
		if (empty($parameter)) {
			$filterStatus = '';
		} else {
			$filterStatus = $title . ' &thinsp; • &thinsp; ' . $parameter;
		}			
		return $filterStatus;
	}

	

	public function dataMatchesFilter(\model\HorseStore $storeCard, \visual\StoreUrl $urlStore): bool
	{
		// Фильтруем по Полу, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagGender)) {
			$compareText = mb_strtolower($storeCard->getGender(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagGender) === false) {
				return false;
			}
		}

		// Фильтруем по Возрастной категории, если тэг присутствует в запросе
		if (!empty($urlStore->paramTagAge)) {
			$compareText = mb_strtolower($storeCard->getAgeCategory(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagAge) === false) {
				return false;
			}
		}

		// Фильтруем по Породе, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagBreed)) {
			$compareText = mb_strtolower($storeCard->getBreed(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagBreed) === false) {
				return false;
			}
		}

		// Фильтруем по Характеру, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagCharacter)) {
			$compareText = mb_strtolower($storeCard->getCharacter(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagCharacter) === false) {
				return false;
			}
		}

		// Фильтруем по Месту рождения, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagHomeland)) {
			$compareText = mb_strtolower($storeCard->getHomeland(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagHomeland) === false) {
				return false;
			}
		}

		// Фильтруем по Вариантам использования, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagUsing)) {
			$compareText = mb_strtolower($storeCard->getFeatures(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagUsing) === false) {
				return false;
			}
		}


		// Фильтруем по Весовой категории, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagWeight)) {
			$compareText = mb_strtolower($storeCard->getWeightCategory(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagWeight) === false) {
				return false;
			}
		}

		
		// Фильтруем по Статусу заказа, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagStatus)) {
			$compareText = mb_strtolower($storeCard->getOrderStatus(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagStatus) === false) {
				return false;
			}
		}
		
		
		// Фильтруем по Вариантам сервиса, если тэг присутствует в запросе, но не найден в описании
		if (!empty($urlStore->paramTagService)) {
			$compareText = mb_strtolower($storeCard->getServices(), 'UTF-8');
			if (strpos($compareText, $urlStore->paramTagService) === false) {
				return false;
			}
		}

		// Совпадения найдены, фильтр пройден
		return true;
	}

}
