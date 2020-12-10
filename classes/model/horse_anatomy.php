<?PHP

// Класс работает с анатомическими свойствами объекта:
// Инициализирует, валидирует, выдаёт в человеческом виде на фронтенд

namespace model;
use model;

Class HorseAnatomy extends Horse
{
	public $anatomyAge = 0; // возраст, мес.
	public $anatomyGender = 0; // пол
	public $anatomyWeight = 0; // вес, кг.
	public $anatomyBreed = ''; // порода	
	
	public $dateAdded = ''; // дата добавления
	
	const AGE_MIN = 1; // минимальный существующий возраст, мес.
	const AGE_MAX = 60 * 12; // максимальный существующий возраст, мес.

	const WEIGHT_MIN = 200; // минимальный существующий вес, кг.
	const WEIGHT_MAX = 1600; // максимальный существующий вес, кг.

	const GENDER_STALLION = 1; // жеребец
	const GENDER_MARE = 2; // кобыла	
	

	// Инициализация при создании объекта
	public function __construct(string $age, string $gender, string $weight, string $breed, string $dateAdded, bool $autoValidation = true)
	{
		$this->anatomyAge = intval($age);
		$this->anatomyGender = intval($gender);
		$this->anatomyWeight = intval($weight);
		$this->anatomyBreed = $breed;

		// если дата указана
		if ($dateAdded !== '') {
			$this->dateAdded = $dateAdded;
		}
		// если дата не указана, значит объект создан только что
		else {
			$dateNow = new \DateTime();
			$this->dateAdded = $dateNow->format('Y-m-d H:i:s');
		}
		
		// используем валидатор текущего класса, если это нужно
		$this->autoValidation = $autoValidation;
		self::validate();
	}

	
	// Проверка правильности анатомических данных
	public function validate()
	{	
		// Исключаем избыточную валидацию при наследовании
		if (!$this->autoValidation) return;
		
		$this->correct = (
			($this->isCorrectAge()) &&
			($this->isCorrectGender()) &&
			($this->isCorrectWeight()) &&
			($this->isCorrectBreed())
		);
	}


	// Проверка возраста
	public function isCorrectAge(): bool
	{
		return (
			($this->anatomyAge >= self::AGE_MIN) && 
			($this->anatomyAge <= self::AGE_MAX)
		);
	}


	// Проверка веса
	public function isCorrectWeight(): bool
	{
		return (
			($this->anatomyWeight >= self::WEIGHT_MIN) && 
			($this->anatomyWeight <= self::WEIGHT_MAX)
		);
	}


	// Проверка пола
	public function isCorrectGender(): bool
	{
		return (
			($this->anatomyGender == self::GENDER_STALLION) || 
			($this->anatomyGender == self::GENDER_MARE)
		);
	}

	
	// Возвращаем дату в человеческой форме
	public function getDateAdded(): string
	{	
		return LibString_GetDateNice($this->dateAdded);
	}

	
	// Учитываем взросление лошади с момента добавления в каталог
	private function getAgeActual(): int
	{
		// если возраст изначально некорректный, взросление не вычисляем
		if (!$this->isCorrectAge()) {
			return $this->anatomyAge;
		}

		$dateFrom = new \DateTime();
		$dateTo = new \DateTime($this->dateAdded);

		$interval = $dateFrom->diff($dateTo);

		$years = $interval->format('%y');
		$months = $interval->format('%m');

		$ageActual = $this->anatomyAge + ($years * 12) + $months;

		return $ageActual;
	}

	// Возвращаем комментарий к возрастному периоду лошади
	public function getAgeCategory(): string
	{
		$ageActual = $this->getAgeActual();

		if ($ageActual >= 18 * 12) {
			$agePeriod = 'Старая лошадь';
		}
		elseif ($ageActual >= 15 * 12) {
			$agePeriod = 'Возрастная лошадь';
		}
		elseif ($ageActual >= 6 * 12) {
			$agePeriod = 'Взрослая лошадь';
		}
		elseif ($ageActual >= 4 * 12) {
			$agePeriod = 'Молодая лошадь';
		}
		elseif ($ageActual >= 3 * 12) {
			$agePeriod = 'Трёхлеток';
		}
		elseif ($ageActual >= 2 * 12) {
			$agePeriod = 'Двухлеток';
		}
		elseif ($ageActual >= 1.5 * 12) {
			$agePeriod = 'Полуторник';
		}
		elseif ($ageActual >= 12) {
			$agePeriod = 'Годовичок';
		}
		elseif ($ageActual > 0) {
			$agePeriod = 'Жеребёнок';
		} else {
			$agePeriod = 'Ошибка';
		}
		
		if ($ageActual < self::AGE_MIN) {
			$agePeriod = 'Не допустимо';
		}

		$agePeriod = LibString_TextToTagList($agePeriod, ',', ', &thinsp;', $this->url, 'tag_age');
		return $agePeriod;
	}

	
	// Возвращаем число в формате год + мес для более человеческого вида
	public function getAge(): string
	{
		// если возраст изначально некорректный, не форматируем результат

		$dateFrom = new \DateTime();
		$dateTo = new \DateTime();
		$dateTo->modify('+' . $this->getAgeActual() . ' month');

		$interval = $dateFrom->diff($dateTo);

		$years = $interval->format('%y');
		if ($years > 0) {
			$yearRemark = LibString_GetQuantRemark($years, 'год', 'лет', 'года');
		} else {
			$years = '';
			$yearRemark = '';
		}

		$months = $interval->format('%m');
		if ($months > 0) {
			$monthRemark = LibString_GetQuantRemark($months, 'месяц', 'месяцев', 'месяца');
		} else {
			$months = '';
			$monthRemark = '';
		}


		$result = "$years $yearRemark $months $monthRemark";

		$result .= '&thinsp; (' . $this->getAgeCategory($this->url) . ')';

		return $result;
	}


	// Возвращаем весовую категорию
	public function getWeightCategory(): string
	{
		if ($this->anatomyWeight > 600) {
			$category = 'Тяжёлый вес';
		}
		elseif ($this->anatomyWeight >= 400) {
			$category = 'Средний вес';
		}
		elseif ($this->anatomyWeight > 0) {
			$category = 'Лёгкий вес';
		} else {
			$category = 'Ошибка';
		}
		
		if ($this->anatomyWeight < self::WEIGHT_MIN) {
			$category = 'Не допустимо';
		}		

		$category = LibString_TextToTagList($category, ',', ', &thinsp;', $this->url, 'tag_weight');
		return $category;
	}


	// Вес лошади с обозначением
	public function getWeight(): string
	{
		$result = "$this->anatomyWeight кг.";
		$result .= '&thinsp; (' . $this->getWeightCategory($this->url) . ')';

		return $result;
	}


	// Пол лошади
	public function getGender(): string
	{
		if ($this->anatomyGender == self::GENDER_STALLION) {
			$gender = LibString_TextToTagList('Жеребец', ',', ', &thinsp;', $this->url, 'tag_gender');
			return $gender;
		}
		elseif ($this->anatomyGender == self::GENDER_MARE) {
			$gender = LibString_TextToTagList('Кобыла', ',', ', &thinsp;', $this->url, 'tag_gender');
			return $gender;
		}
		else {
			return '(Бесполый)';
		}
	}

	
	// 4.1 Вывод сведений о породе
	public function getBreed()
	{
		if ($this->isCorrectBreed()) {
			$breed = LibString_TextToTagList($this->anatomyBreed, ',', ', &thinsp;', $this->url, 'tag_breed');
			return $breed;			
		} else {
			return '(Беспородный)';
		}
	}


	// 4.2 Проверка сведений о породе
	public function isCorrectBreed(): bool
	{
		$this->anatomyBreed = trim($this->anatomyBreed);
		return (!empty($this->anatomyBreed) && ($this->anatomyBreed !== ''));
	}
	

	// Инициализация адреса страницы для формирования списков тегов
	public function setUrl(string $url = '')
	{	
		$this->url = $url;
	}

}
