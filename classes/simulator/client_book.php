<?PHP

// Класс для работы с тестовой файловой базой данных активности клиента

namespace simulator;
use model\Client;

Class ClientBook extends Client
{
	public $clientDir = '';
	public $bookInfo = array();
	public $bookFilename = '';


	// Инициализация клиента через идентификатор сессии
	public function __construct(string $clientId = '', string $root = '')
	{
		parent::__construct($clientId);

		$this->clientDir = $root . 'base/clients/' . $this->id . '/';

		if (!is_dir($this->clientDir)) {
			mkdir($this->clientDir, 0777);
		}

		$this->bookFilename = $this->clientDir . 'book.ini';

		if (!file_exists($this->bookFilename)) {
			$bookData = array();
			file_put_contents($this->bookFilename, $bookData);
		} else {
			$bookData = file($this->bookFilename);
		}

		$bookDataCount = count($bookData);
		$this->bookInfo = array();

		// формируем ассоциативный массив из строк файла, записанных в формате "key | value"
		for ($i = 0; $i < $bookDataCount; $i++) {
			$line = $bookData[$i];
			$lineParts = explode('|', $line);
			if (count($lineParts) < 2) {
				continue;
			}
			$key = $lineParts[0];
			$this->bookInfo[$key] = trim($lineParts[1]);
		}
	}


	// Установка статуса заказа
	public function setOrderStatus($index, $orderSigned, $orderPayed)
	{
		parent::setOrderStatus($index, $orderSigned, $orderPayed);

		$this->bookInfo[$index] = intval($orderSigned) + intval($orderPayed);
		$this->saveBook();
	}


	// Сброс состояния
	public function reset()
	{
		parent::reset();
		
		array_splice($this->bookInfo, 0);
		$this->saveBook();
	}


	// Считывает статус заказа из файла
	public function getOrderStatus($orderId)
	{
		if (isset($this->bookInfo[$orderId])) {
			$this->orderSigned = ($this->bookInfo[$orderId] > 0);
			$this->orderPayed = ($this->bookInfo[$orderId] > 1);
		} else {
			$this->orderSigned = false;
			$this->orderPayed = false;
		}
	}

	// Сохранение состояний заказов в файл
	public function saveBook()
	{
		$bookData = array();

		$bookKeys = array_keys($this->bookInfo);
		$cardKeysCount = count($bookKeys);

		for ($i = 0; $i < $cardKeysCount; $i++) {
			$orderId = $bookKeys[$i];
			$bookData[] = $orderId  . '|' . $this->bookInfo[$orderId] . PHP_EOL;
		}

		file_put_contents ($this->bookFilename, $bookData);
	}



}
