<?PHP

// Класс сохраняет данные класса в файл тестовой базы

namespace simulator;
use model;

Class HorseFileSaver extends model\HorseStore
{	

	// сохранение полей в файл
	public function saveToFile($index, $root = '')
	{
		$cardData = array();
		
		$cardData[] = 'id|' . $index . PHP_EOL;

		$cardData[] = 'age|' . $this->anatomyAge . PHP_EOL;
		$cardData[] = 'gender|' . $this->anatomyGender . PHP_EOL;
		$cardData[] = 'weight|' . $this->anatomyWeight . PHP_EOL;
		$cardData[] = 'breed|' . $this->anatomyBreed . PHP_EOL;

		$cardData[] = 'datetime|' . $this->dateAdded . PHP_EOL;
		$cardData[] = 'name|' . $this->socialName . PHP_EOL;
		$cardData[] = 'homeland|' . $this->socialHomeland . PHP_EOL;
		$cardData[] = 'character|' . $this->socialCharacter . PHP_EOL;
		$cardData[] = 'features|' . $this->socialFeatures . PHP_EOL;
		$cardData[] = 'pricerent|' . $this->priceRent . PHP_EOL;
		$cardData[] = 'pricebuy|' . $this->priceBuy . PHP_EOL;
		$cardData[] = 'canrent|' . $this->canRent . PHP_EOL;
		$cardData[] = 'canbuy|' . $this->canBuy . PHP_EOL;
		$cardData[] = 'ordersigned|' . $this->orderSigned . PHP_EOL;
		$cardData[] = 'orderpayed|' . $this->orderPayed . PHP_EOL;
		$cardData[] = 'clientid|' . $this->clientId . PHP_EOL;		
		$cardData[] = 'avatar|' . $this->filenameAvatar . PHP_EOL;

		file_put_contents ($root . 'base/cards/' . $index . '.ini', $cardData);
	}

}
