<?PHP

session_start();

if (!$_POST) {
	return;
}

// Путь до корневой директории
$root = '../';

// Подключаем необходимые ресурсы
require $root . 'library/lib_string.php';
require $root . 'library/lib_url.php';

require $root . 'classes/model/horse.php';
require $root . 'classes/model/horse_anatomy.php';
require $root . 'classes/model/horse_social.php';
require $root . 'classes/model/horse_store.php';

// Тестировочные классы
require $root . 'classes/simulator/horse_data_set.php';
require $root . 'classes/simulator/horse_file_saver.php';
require $root . 'classes/simulator/horse_file_reader.php';
require $root . 'classes/simulator/horse_file_generator_bad.php';
require $root . 'classes/simulator/horse_file_generator_good.php';

// Визуальные классы
require $root . 'classes/visual/store_url.php';
require $root . 'classes/visual/store_card_decorator.php';

// Инициализируем переменные из запроса
$cardId = $_POST['cardId'];
$orderSigned = $_POST['orderSigned'];
$orderPayed = $_POST['orderPayed'];

// Создаём набор данных для автоматической коррекции
$horseDataSet = new \simulator\HorseDataSet($root);	

// Считываем старую карточку, которую собираемся исправлять
$storeCard = new \simulator\HorseFileReader($cardId, $root);

// Генерируем новую со случайными корректными данными
$validCard = $horseDataSet->getRandomCard($cardId, 'client');

// ВАЛИДАЦИЯ

// Если способы применения лошади не корректны, берём данные из валидной карточки
if (!$storeCard->isCorrectFeatures()) {
	$storeCard->socialFeatures = $validCard->socialFeatures;
}

// Если цена аренды не корректна, берём её из валидной карточки
if (!$storeCard->isCorrectPriceRent()) {
	$storeCard->canRent = $validCard->canRent;
	$storeCard->priceRent = $validCard->priceRent;
}

// Если цена покупки не корректна, берём её из валидной карточки
if (!$storeCard->isCorrectPriceBuy()) {
	$storeCard->canBuy = $validCard->canBuy;
	$storeCard->priceBuy = $validCard->priceBuy;
}

// Если соотношение цены покупки и аренды не корректно, берём весь набор цен из валидной карточки
if (!$storeCard->isCorrectPriceBalance() || !$storeCard->isCorrectServices()) {
	$storeCard->canRent = $validCard->canRent;
	$storeCard->priceRent = $validCard->priceRent;
	$storeCard->canBuy = $validCard->canBuy;
	$storeCard->priceBuy = $validCard->priceBuy;
}

// Если имя лошади не корректно, берём его из валидной карточки
if (!$storeCard->isCorrectName()) {
	$storeCard->socialName = $validCard->socialName;
}

// Если вес лошади не корректен, берём его из валидной карточки
if (!$storeCard->isCorrectWeight()) {
	$storeCard->anatomyWeight = $validCard->anatomyWeight;
}

// Если возраст лошади не корректен, берём его из валидной карточки
if (!$storeCard->isCorrectAge()) {
	$storeCard->anatomyAge = $validCard->anatomyAge;
	$storeCard->dateAdded = $validCard->dateAdded;
}

if (!$storeCard->isCorrectBreed()) {
	$storeCard->anatomyBreed = $validCard->anatomyBreed;
}

if (!$storeCard->isCorrectCharacter()) {
	$storeCard->socialCharacter = $validCard->socialCharacter;
}

if (!$storeCard->isCorrectGender()) {
	$storeCard->anatomyGender = $validCard->anatomyGender;
}

if (!$storeCard->isCorrectHomeland()) {
	$storeCard->socialHomeland = $validCard->socialHomeland;
}

// Если аватар не корректен, берём фото из валидной карточки
if (!$storeCard->isCorrectAvatar()) {
	$storeCard->filenameAvatar = $validCard->filenameAvatar;
}

$storeCard->validate();
$storeCard->setOrderStatus(false, false, '');

//$storeCard->filenameAvatar = $storeCardOld->filenameAvatar;
$storeCard->saveToFile($cardId, $root);

// Устанавливаем параметры базового адреса для фильтрующих ссылок-тегов
$urlStore = new \visual\StoreUrl($_POST['pageUrl']);
$urlStore->paramTagService = $_POST['tagService'];
$urlStore->paramMode = 'admin';

// создаём экземпляр декоратора карточек (для зебра-полос)
$cardDecorator = new \visual\StoreCardDecorator();

// Рендерим карточку, учитывая изменившиеся параметры
include $root . 'templates/store_card.html';