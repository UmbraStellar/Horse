<?PHP

// Тестовый класс генерирует плохой набор данных для файловой базы
// Карточка может содержать некорректные данные во всех полях

namespace simulator;

Class HorseRandomBad extends HorseFileSaver
{

	// Инициализация через индекс файла
	public function __construct(int $index, HorseDataSet $dataSet)
	{
		$cardInfo = array();

		$cardInfo['age'] = rand(0,170);
		$cardInfo['gender'] = rand(0,2);
		$cardInfo['weight'] = rand(0,1000);

		$randomDate = rand(2019, 2020) . '-' . rand(1,12) . '-' . rand(1,29);
		$randomTime = rand(0,23) . ':' . rand(0,59) . ':' . rand(0,59);

		$cardInfo['datetime'] = $randomDate . ' ' . $randomTime;


		$cardInfo['name'] = $dataSet->getRandomName($cardInfo['gender'], 0);
		$cardInfo['character'] = $dataSet->getRandomCharacter(0, 4);
		$cardInfo['features'] = $dataSet->getRandomFeatures(0, 3);

		$cardInfo['breed'] = $dataSet->getRandomBreed(0);
		$cardInfo['homeland'] = $dataSet->getRandomHomeland(0);

		$cardInfo['pricerent'] = rand(0,90) * 10;
		$cardInfo['pricebuy'] = rand(0, 5000) * 10;
		$cardInfo['canrent'] = rand(0,1);
		$cardInfo['canbuy'] = rand(0,1);
		
		$cardInfo['avatar'] = $dataSet->getRandomAvatar($index);

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

		// возможно договор уже подписан
		$orderSigned = rand(0, $cardInfo['canrent']);

		// если договор подписан, то возможно уже и оплачен
		if ($orderSigned) {
			$orderPayed = rand(0, $cardInfo['canbuy']);
		} else {
			$orderPayed = 0;
		}

		if ($orderPayed) {
			$clientId = md5(rand(1,9999999));
		} else {
			$clientId = '';
		}

		// устанавливаем статус заказа
		$this->setOrderStatus($orderSigned, $orderPayed, $clientId);

		$this->saveToFile($index);
	}
}
