<?PHP

// Класс описывает ценовую политику и статус лошади на витрине сервиса.
// Может использоваться как класс хелпер при валидации данных с формы ввода цен.

namespace model;
use model;

Class HorseStore extends HorseSocial
{
	public $available = false; // доступен ли объект для аренды или покупки

	public $priceRent = 0; // цена за час аренды
	public $priceBuy = 0; // цена выкупа

	public $canRent = false; // доступен ли объект для аренды
	public $canBuy = false; // доступен ли объект для покупки

	public $orderInProcess = false; // заказ в процессе выполнения
	public $orderSigned = false; // договор подписан
	public $orderPayed = false; // заказ оплачен
	
	public $clientId = ''; // айди клиента, оплатившего заказ (бронирует карточку)
	public $filenameAvatar = ''; // фото лошади
	
	const ORDER_SIGNED = true;
	const ORDER_PAYED = true;

	// Инициализация при создании объекта
	public function __construct($age, $gender, $weight, $dateAdded, $name, $homeland, $character, $breed, $features, int $priceRent, int $priceBuy, bool $canRent, bool $canBuy, $filenameAvatar)
	{
		parent::__construct($age, $gender, $weight, $dateAdded, $name, $homeland, $character, $breed, $features, self::DONT_VALIDATE);

		$this->priceRent = intval($priceRent);
		$this->priceBuy = intval($priceBuy);

		$this->canRent = $canRent;
		$this->canBuy = $canBuy;
		
		$this->filenameAvatar = $filenameAvatar;

		// Это последний этап сборки, включаем валидацию
		$this->autoValidation = true;
		self::validate();
	}


	// Проверка доступности объекта для заказа в витрине
	public function setOrderStatus(bool $orderSigned, bool $orderPayed, string $clientId = '')
	{
		if (!$orderPayed) {
			$clientId = '';
		}
		
		$this->orderSigned = $orderSigned;
		$this->orderPayed = $orderPayed;
		$this->clientId = $clientId;
		
		$this->orderInProcess = (
			// лошадь можно арендовать или купить
			($this->canRent || $this->canBuy) &&

			// договор не подписан либо ещё не оплачен
			($this->orderSigned && $this->orderPayed)
		);

		// Лошадь доступна для показа, если:
		$this->available = (
			// все данные корректны
			($this->correct) &&

			// лошадь можно арендовать или купить
			($this->canRent || $this->canBuy) &&

			// договор не подписан либо ещё не оплачен
			(!$this->orderSigned || !$this->orderPayed)
		);
	}


	// Проверяем корректность данных
	public function validate()
	{
		parent::validate();

		$this->correct = (
			$this->correct &&
			$this->isCorrectPriceRent() &&
			$this->isCorrectPriceBuy() &&
			$this->isCorrectServices() &&
			$this->isCorrectPriceBalance() &&
			$this->isCorrectAvatar()
		);
	}

	
	// Проверка наличия аватара
	public function isCorrectAvatar()
	{
		return (!empty($this->filenameAvatar));
	}
	
	
	// Возвращает имя аватара
	public function getPhotoFilename()
	{
		return $this->filenameAvatar;
	}
	

	// Возвращает цену для шапки
	public function getPrice()
	{
		if (($this->canRent) && ($this->priceRent > 0)) {
			$price = number_format($this->priceRent, 0, '.', ' ') . ' руб/час';
		}
		elseif (($this->canBuy) && ($this->priceBuy > 0)) {
			$price = number_format($this->priceBuy, 0, '.', ' ') . ' руб.';
		} else {
			$price = '0 руб.';
		}
		return $price;
	}

	
	// Возвращает стоимость аренды для каталога
	public function getPriceRent()
	{
		if (!$this->canRent) {
			return 'Не сдаётся в аренду';
		}
		
		$prices = array();
		$prices[] = number_format($this->priceRent, 0, '.', ' ') . ' руб/час';

		if (!$this->isCorrectPriceRent()) {
			$prices[] = 'Ошибка';
		}

		if (!$this->isCorrectPriceBalance()) {
			$prices[] = 'Неверный баланс';
		}

		$price = implode('&thinsp; • &thinsp;', $prices);

		if (empty($price)) {
			$price = 'Не указано (Ошибка)';
		}

		return $price;
	}


	// Возвращает цену покупки для каталога
	public function getPriceBuy()
	{
		if (!$this->canBuy) {
			return 'Не продаётся';
		}
		
		$prices = array();
		$prices[] = number_format($this->priceBuy, 0, '.', ' ') . ' руб.';

		if (!$this->isCorrectPriceBuy()) {
			$prices[] = 'Ошибка';
		}

		if (!$this->isCorrectPriceBalance()) {
			$prices[] = 'Неверный баланс';
		}

		$price = implode('&thinsp; • &thinsp;', $prices);

		if (empty($price)) {
			$price = 'Не указано (Ошибка)';
		}

		return $price;
	}


	// Проверка цены аренды
	public function isCorrectPriceRent(): bool
	{
		return ($this->canRent && ($this->priceRent > 0)) || (!$this->canRent && ($this->priceRent == 0));
	}


	// Проверка цены покупки
	public function isCorrectPriceBuy(): bool
	{
		return ($this->canBuy && ($this->priceBuy > 0)) || (!$this->canBuy && ($this->priceBuy == 0));
	}


	// Согласно ценовой политике сервиса, аренда всегда дешевле покупки
	public function isCorrectPriceBalance(): bool
	{
		// ошибка, если можно арендовать, но цена аренды не указана или равна нулю
		if ($this->canRent && ($this->priceRent <= 0)) {
			return false;
		}
		// ошибка, если можно купить, но цена покупки не указана или равна нулю
		elseif ($this->canBuy && ($this->priceBuy <= 0)) {
			return false;
		}
		// ошибка, если можно лбо арендовать либо купить, то цена аренды не может быть больше или равна покупки
		elseif (($this->canRent && $this->canBuy) && ($this->priceRent >= $this->priceBuy)) {
			return false;
		}

		// в иных ситуациях нет ошибки
		return true;
	}
	

	// Возвращает список доступных сервисов
	public function getServices()
	{
		// если набор сервисов некорректный, выводим ремарку
		if (!$this->isCorrectServices()) {
			$services = 'Не указано (Ошибка)';
		} 
		// если существует хотябы один сервис, формируем список
		else {
			$orderStatus = array();

			if ($this->canRent) {
				$orderStatus[] = 'Аренда';
			}

			if ($this->canBuy) {
				$orderStatus[] = 'Покупка';
			}

			// объединяем массив в строку
			$services = implode(',', $orderStatus);			
		}

		// выводим в виде списка ссылок, по которым можно отфильтровать витрину
		$serviceList = LibString_TextToTagList($services, ',', '&thinsp; • &thinsp;', $this->url, 'tag_service');
		return $serviceList;
	}


	// Проверка наличия хотябы одного сервиса
	public function isCorrectServices(): bool
	{
		return (($this->canRent || $this->canBuy));
	}


	// Возвращает статус договора для каталога
	public function getOrderStatus()
	{
		$orderStatus = array();

		// статус подписания
		if ($this->orderSigned) {
			$orderStatus[] = 'Договор подписан';
		}

		// статус оплаты
		if ($this->orderPayed) {
			$orderStatus[] = 'Счёт оплачен';
		} else {
			if ($this->orderSigned) {
				$orderStatus[] = 'Не оплачен (В ожидании)';
			}
		}

		// статус выполнения
		if ($this->orderSigned && $this->orderPayed) {
			$orderStatus[] = 'Выполняется';
		}

		// объединяем массив в строку
		$status = implode(',', $orderStatus);

		// если статус пустой, значит заказов нет
		if (empty($status)) {
			$status = 'Нет брони';
		}

		// выводим в виде списка ссылок, по которым можно отфильтровать витрину
		$tagList = LibString_TextToTagList($status, ',', '&thinsp; • &thinsp;', $this->url, 'tag_status');
		return $tagList;
	}


	// Возвращает параметр цветовой идентификации карточки
	public function getItemColorTheme()
	{		
		if ($this->orderSigned && !$this->orderPayed && $this->correct) {
			return 'black-card';
		}
		if ($this->orderInProcess && $this->correct) {
			return 'green-card';
		}
		if (!$this->correct || !$this->available) {
			return 'red-card';
		} else {
			return 'blue-card';
		}
	}

}
