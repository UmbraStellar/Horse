<?PHP

// Класс наследует анатомию лошади и работает с поведенческими свойствами:
// Инициализирует, валидирует, выдаёт в человеческом виде на фронтенд

namespace model;
use model;

Class HorseSocial extends HorseAnatomy
{
	public $socialName = ''; // кличка
	public $socialHomeland = ''; // место рождения
	public $socialCharacter = ''; // поведение, повадки, характер
	public $socialFeatures = ''; // для чего используется лошадь


	// Инициализация при создании объекта
	public function __construct(int $age, int $gender, int $weight, string $dateAdded, string $name, string $socialHomeland, string $socialCharacter, string $breed, string $socialFeatures, bool $autoValidation = true)
	{
		parent::__construct($age, $gender, $weight, $breed, $dateAdded, self::DONT_VALIDATE);

		$this->socialName = $name;
		$this->socialHomeland = $socialHomeland;
		$this->socialCharacter = $socialCharacter;
		$this->socialFeatures = $socialFeatures;

		// используем валидатор текущего класса, если это нужно
		$this->autoValidation = $autoValidation;
		self::validate();
	}


	// Наследуем проверку корректности данных
	public function validate()
	{
		// Исключаем избыточную валидацию при наследовании
		if (!$this->autoValidation) return;

		parent::validate();

		$this->correct = (
			($this->correct) &&
			($this->isCorrectName()) &&
			($this->isCorrectCharacter()) &&
			($this->isCorrectHomeland()) &&
			($this->isCorrectFeatures())
		);
	}


	// 1.1 Вывод имени
	public function getName()
	{
		if ($this->isCorrectName()) {
			return $this->socialName;
		} else {
			return '(Безымянный)';
		}
	}


	// 1.2 Проверка имени
	public function isCorrectName(): bool
	{
		$this->socialName = trim($this->socialName);
		return (!empty($this->socialName) && ($this->socialName !== ''));
	}


	// 2.1 Вывод области применения
	public function getFeatures()
	{
		if ($this->isCorrectFeatures()) {
			$tagList = LibString_TextToTagList($this->socialFeatures, ',', '&thinsp; • &thinsp;', $this->url, 'tag_using');
			return $tagList;
		} else {
			return 'Неизвестно (Ошибка)';
		}
	}


	// 2.2 Проверка области применения
	public function isCorrectFeatures(): bool
	{
		$this->socialFeatures = trim($this->socialFeatures);
		return (!empty($this->socialFeatures) && ($this->socialFeatures !== ''));
	}


	// 3.1 Вывод сведений о повадках
	public function getCharacter()
	{
		if ($this->isCorrectCharacter()) {
			$character = LibString_TextToTagList($this->socialCharacter, ',', '&thinsp; • &thinsp;', $this->url, 'tag_character');
			return $character;
		} else {
			return 'Неизвестно (Ошибка)';
		}
	}


	// 3.2 Проверка сведений о повадках
	public function isCorrectCharacter(): bool
	{
		$this->socialCharacter = trim($this->socialCharacter);
		return (!empty($this->socialCharacter) && ($this->socialCharacter !== ''));
	}


	// 4.1 Вывод сведений о месте рождения
	public function getHomeland()
	{
		if ($this->isCorrectHomeland()) {
			$homeland = LibString_TextToTagList($this->socialHomeland, ',', ', &thinsp;', $this->url, 'tag_bplace');
			return $homeland;
		} else {
			return 'Неизвестно (Ошибка)';
		}
	}


	// 4.2 Проверка сведений о месте рождения
	public function isCorrectHomeland(): bool
	{
		$this->socialHomeland = trim($this->socialHomeland);
		return (!empty($this->socialHomeland) && ($this->socialHomeland !== ''));
	}

}
