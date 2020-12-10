<?PHP

// Тестовый класс загружает данные из файловой базы по индексу файла

namespace simulator;

Class HorseFileReader extends HorseFileSaver
{

	// Инициализация через индекс файла
	public function __construct($index, $root = '')
	{
		$cardFilename = $root . 'base/cards/' . $index . '.ini';
		if (!file_exists($cardFilename)) {
			return;
		}
		$cardData = file($cardFilename);
		$cardDataCount = count($cardData);

		// формируем ассоциативный массив из строк файла, записанных в формате "key | value"
		for ($i = 0; $i < $cardDataCount; $i++) {
			$line = $cardData[$i];
			$lineParts = explode('|', $line);
			if (count($lineParts) < 2) {
				continue;
			}
			$key = $lineParts[0];
			$cardInfo[$key] = trim($lineParts[1]);
		}
		
		// инициализируем
		parent::__construct(
			$cardInfo['age'],
			$cardInfo['gender'],
			$cardInfo['weight'],
			$cardInfo['datetime'],
			$cardInfo['name'],
			$cardInfo['homeland'],
			$cardInfo['character'],
			$cardInfo['breed'],
			$cardInfo['features'],
			(int) $cardInfo['pricerent'],
			(int) $cardInfo['pricebuy'],
			(int) $cardInfo['canrent'],
			(int) $cardInfo['canbuy'],
			$cardInfo['avatar']
		);
		
		// устанавливаем статус заказа
		$this->setOrderStatus((int) $cardInfo['ordersigned'], (int) $cardInfo['orderpayed'], $cardInfo['clientid']);
	}	
	
}
