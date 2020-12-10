<?PHP

// Абстрактный класс описывает базовую структуру объекта Лошадь,
// сборкой которого будет заниматься конвейер HorseAnatomy -> HorseSocial -> HorseStore.

// Если поля пустые или некорректные, объект помечается некоректным.
// Некорректные объекты не представляют ценности для клиента и видны только админам.

namespace model;

Abstract Class Horse
{
	public $anatomyAge; // возраст, мес.
	public $anatomyGender; // пол
	public $anatomyWeight; // вес, кг.
	public $anatomyBreed; // порода
	
	public $dateAdded; // дата добавления, для актуализации возраста
	
	public $socialName; // кличка
	public $socialHomeland; // место рождения
	public $socialCharacter; // поведение, повадки, характер
	public $socialFeatures; // для чего используется

	public $correct; // статус корректности данных
	public $autoValidation;  // разрешать авто валидацию
	
	public $url = ''; // текущий адрес страницы
	
	const DONT_VALIDATE = false;
	
	// Валидация данных
	abstract public function validate();		
}
