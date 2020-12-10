<?PHP

// Тестовый класс генерирует плохой набор данных для файловой базы
// Карточка может содержать некорректные данные во всех полях

namespace simulator;

Class HorseRandomGood extends HorseFileSaver
{

	// Инициализация через индекс файла
	public function __construct(int $index, bool $isBooked, HorseDataSet $dataSet)
	{
		$cardInfo = array();

		$cardInfo['age'] = rand(self::AGE_MIN,170);
		$cardInfo['gender'] = rand(1,2);
		$cardInfo['weight'] = rand(self::WEIGHT_MIN,1000);
		
		$cardInfo['name'] = $dataSet->getRandomName($cardInfo['gender'], 1);		
		$cardInfo['character'] = $dataSet->getRandomCharacter(2, 4);
		$cardInfo['features'] = $dataSet->getRandomFeatures(2, 3);
		$cardInfo['breed'] = $dataSet->getRandomBreed(1);
		$cardInfo['homeland'] = $dataSet->getRandomHomeland(1);

		$cardInfo['canrent'] = rand(0,1);
		if ($cardInfo['canrent']) {
			$cardInfo['pricerent'] = rand(10,50) * 10;
		} else {
			$cardInfo['pricerent'] = 0;
		}

		$cardInfo['canbuy'] = rand(0,1);
		if ($cardInfo['canbuy']) {
			$cardInfo['pricebuy'] = rand(100, 500) * 100;
		} else {
			$cardInfo['pricebuy'] = 0;
		}

		if (!$cardInfo['canrent'] && !$cardInfo['canbuy']) {
			$cardInfo['canrent'] = 1;
			$cardInfo['pricerent'] = rand(10,50) * 10;
		}

		$cardInfo['avatar'] = $dataSet->getRandomAvatar($index);

		// случайная дата и время добавления карточки в каталог
		$randomDate = rand(2020, 2020) . '-' . rand(11,12) . '-' . rand(1,5);
		$randomTime = rand(0,23) . ':' . rand(0,59) . ':' . rand(0,59);
		$cardInfo['datetime'] = $randomDate . ' ' . $randomTime;
		
		// Инициализируем
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
			$cardInfo['pricerent'],
			$cardInfo['pricebuy'],
			$cardInfo['canrent'],
			$cardInfo['canbuy'],
			$cardInfo['avatar']
		);

		if ($isBooked) {
			// Договор подписан и кем-то оплачен
			$clientId = md5(rand(1,9999999));
			$this->setOrderStatus(self::ORDER_SIGNED, self::ORDER_PAYED, $clientId);
		} else {
			// Договор не подписан и ещё не оплачен
			$clientId = '';
			$this->setOrderStatus(!self::ORDER_SIGNED, !self::ORDER_PAYED, $clientId);
		}

		$this->saveToFile($index, $dataSet->root);
	}
}
