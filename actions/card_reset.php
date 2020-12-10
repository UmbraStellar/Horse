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

// Визуальные классы
require $root . 'classes/visual/store_url.php';
require $root . 'classes/visual/store_card_decorator.php';

// Тестировочные классы
require $root . 'classes/simulator/horse_file_saver.php';
require $root . 'classes/simulator/horse_file_reader.php';

// Инициализируем переменные из запроса
$cardId = $_POST['cardId'];
$orderSigned = $_POST['orderSigned'];
$orderPayed = $_POST['orderPayed'];

// Загружаем карточку из файла зная его индекс в файловой базе
$storeCard = new \simulator\HorseFileReader($cardId, $root);	

// Снимаем броню, обнуляя статус заказа
$storeCard->setOrderStatus(false, false, '');	
$storeCard->saveToFile($cardId, $root);
	
// Устанавливаем параметры базового адреса для фильтрующих ссылок-тегов
$urlStore = new \visual\StoreUrl($_POST['pageUrl']);
$urlStore->paramTagService = $_POST['tagService'];
$urlStore->paramMode = 'admin';
	
// создаём экземпляр декоратора карточек (для зебра-полос)
$cardDecorator = new \visual\StoreCardDecorator();

// Рендерим карточку, учитывая изменившиеся параметры
include $root . 'templates/store_card.html';