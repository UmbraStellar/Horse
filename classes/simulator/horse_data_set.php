<?PHP

// Класс читает списки с тестовыми данными для симулятора витрины

namespace simulator;
use model\HorseStore;

Class HorseDataSet
{
	public $root; // префикс до корневой директории

	public $characterList = array(); // список характеристик лошади
	public $characterLimit = 0; // лимит списка (последний индекс)

	public $featuresList = array(); // список вариантов использования
	public $featuresLimit = 0; // лимит списка (последний индекс)

	public $breedsList = array(); // список пород лошадей
	public $breedsLimit = 0; // лимит списка (последний индекс)

	public $homelandsList = array(); // список пород лошадей
	public $homelandsLimit = 0; // лимит списка (последний индекс)

	public $namesList = array(); // раздельный список имён для каждого пола
	public $namesLimits = array(); // массив лимитов (последние индексы)

	public $avatarsList = array(); // список фотографий
	public $avatarsLimit = array(); // массив лимитов (последние индексы)



	// считываем ресурсы для генератора рандомных карточек магазина
	public function __construct(string $root = '')
	{
		$this->root = $root;
		// Первый элемент в списках должен быть пустым для шанса генерации ошибочного значения

		// Характеристики лошади
		$this->characterList = file($root . 'data/character.ini');
		$this->characterLimit = count($this->characterList) - 1;

		// Варианты использования лошади
		$this->featuresList = file($root . 'data/features.ini');
		$this->featuresLimit = count($this->featuresList) - 1;

		// Породы лошадей
		$this->breedsList = file($root . 'data/breeds.ini');
		$this->breedsLimit = count($this->breedsList) - 1;

		// Место рождения
		$this->homelandsList = file($root . 'data/homelands.ini');
		$this->homelandsLimit = count($this->homelandsList) - 1;

		// Имена лошадей (1-жеребцов, 2-кобыл)
		$this->namesList[1] = file($root . 'data/names-stallion.ini');
		$this->namesList[2] = file($root . 'data/names-mare.ini');

		$this->namesListLimits[1] = count($this->namesList[1]) - 1;
		$this->namesListLimits[2] = count($this->namesList[2]) - 1;

		// Фотографии лошадей
		$this->avatarsList = array();

		for ($a = 1; $a < 35; $a++) {
			$filename = $root . 'photo/' . $a . '.jpg';
			if (!file_exists($filename)) {
				break;
			} else {
				$filename = str_replace($root, '', $filename);
				$this->avatarsList[] = $filename;
			}
		}
		$this->avatarsLimit = count($this->avatarsList) - 1;

		// Единоразовое перемешивание списка
		shuffle($this->avatarsList);
	}


	// Возвращает случайную фотографию лошади
	public function getRandomAvatar(int $cardIndex): string
	{
		if (isset($this->avatarsList[$cardIndex-1])) {
			return trim($this->avatarsList[$cardIndex-1]);
		} else {
			return '';
		}
	}

	// Возвращает случайное имя в зависимости от пола $genderIndex, начиная с $minIndex
	public function getRandomName(int $genderIndex, int $minIndex): string
	{
		if (($genderIndex == 1) || ($genderIndex == 2)) {
			// Имя определяем из одного из списков для сгенерированного пола
			$namesLimit = $this->namesListLimits[$genderIndex];
			$itemNameIndex = rand($minIndex, $namesLimit);
			return trim($this->namesList[$genderIndex][$itemNameIndex]);
		} else {
			// Имя пустое, если пол неопределён
			return '';
		}
	}

	// Возвращает случайное место рождения
	public function getRandomHomeland(int $minIndex): string
	{
		$randomIndex = rand($minIndex, $this->homelandsLimit);
		return trim($this->homelandsList[$randomIndex]);
	}

	// Возвращает случайную породу из списка
	public function getRandomBreed(int $minIndex): string
	{
		$randomIndex = rand($minIndex, $this->breedsLimit);
		return trim($this->breedsList[$randomIndex]);
	}


	// Возвращает случайный набор характеристик лошади
	public function getRandomCharacter(int $minCount, int $maxCount): string
	{
		// перемешиваем для большей вариативности
		shuffle($this->characterList);

		if ($maxCount > $this->characterLimit) {
			$maxCount = $this->characterLimit;
		}

		$characterCount = rand($minCount, $maxCount);
		$characterParams = array();

		for ($i = 0; $i < $characterCount; $i++) {
			$characterParams[] = trim($this->characterList[$i]);
		}
		return implode(', ', $characterParams);
	}


	// Возвращает случайный набор вариантов использования лошади
	public function getRandomFeatures(int $minCount, int $maxCount): string
	{
		// перемешиваем для большей вариативности
		shuffle($this->featuresList);

		if ($maxCount > $this->featuresLimit) {
			$maxCount = $this->featuresLimit;
		}

		$featuresCount = rand($minCount, $maxCount);
		$featuresParams = array();

		for ($i = 0; $i < $featuresCount; $i++) {
			$featuresParams[] = trim($this->featuresList[$i]);
		}
		return implode(', ', $featuresParams);
	}


	// Генерируем случайную карточку для витрины
	public function getRandomCard(int $index, $generatorMode = 'client'): HorseStore
	{
		// Генератор хороших карточек
		if ($generatorMode == 'client') {
			return (new HorseRandomGood($index, false, $this));
		}
		// Генератор плохих и хороших
		elseif ($generatorMode == 'admin') {
			$rnd = rand(1,4);

			switch ($rnd) {
				case 1:
					// случайная хорошая карточка без брони заказа
					return (new HorseRandomGood($index, false, $this));
					break;
				case 2:
					// случайная хорошая карточка с чужой бронью заказа
					return (new HorseRandomGood($index, true, $this));
					break;
				default:
					// случайная плохая карточка
					return (new HorseRandomBad($index, $this));
			}
		}
	}
}
