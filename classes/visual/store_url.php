<?PHP

// Класс для работы с адресной строкой и параметрами запросов

namespace visual;

Class StoreUrl
{
	public $full; // текущий адрес ссылки
	public $cleared; // адрес ссылки, очищенный от параметров рандомизации и фильтров

	// параметры фильтрации
	public $paramMode; // режим просмотра каталога
	public $paramTagGender; // пол лошади
	public $paramTagAge; // возраст лошади
	public $paramTagBreed; // порода лошади
	public $paramTagCharacter; // характеристики лошади
	public $paramTagHomeland; // место рождения лошади
	public $paramTagUsing; // использование лошади
	public $paramTagService; // вид услуги (аренда, покупка)
	public $paramTagStatus; // статус заказа
	public $paramTagWeight; // весовая категория
	public $paramRandomize; // тип тестовой рандомизации


	// Валидация данных
	public function __construct($url = '')
	{
		// если не задан $url определяем стандартными средствами
		if (empty($url) || ($url === '')) {
			$this->full = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			$this->full = $url;
		}

		// Очищаем ссылку от параметра 'randomize' (строгий поиск параметра, полное совпадение)
		$this->cleared = LibUrl_ClearFromParameter($this->full, 'randomize', true);

		// Продолжаем очищать от любых параметров фильтрации, начинающихся с "tag_", (нестрогий поиск, частичное совпадение)
		$this->cleared = LibUrl_ClearFromParameter($this->cleared, 'tag_', false);
	}

	
	// Определяем параметры адресной строки
	public function getParameters()
	{
		$this->paramRandomize = LibUrl_GetParameterLow('randomize');
		$this->paramMode = LibUrl_GetParameterLow('mode');
		$this->paramTagGender = LibUrl_GetParameterLow('tag_gender');
		$this->paramTagAge = LibUrl_GetParameterLow('tag_age');
		$this->paramTagBreed = LibUrl_GetParameterLow('tag_breed');
		$this->paramTagCharacter = LibUrl_GetParameterLow('tag_character');
		$this->paramTagHomeland = LibUrl_GetParameterLow('tag_bplace');
		$this->paramTagUsing = LibUrl_GetParameterLow('tag_using');
		$this->paramTagService = LibUrl_GetParameterLow('tag_service');
		$this->paramTagStatus = LibUrl_GetParameterLow('tag_status');
		$this->paramTagWeight = LibUrl_GetParameterLow('tag_weight');
	}

}
