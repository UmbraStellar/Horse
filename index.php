<?PHP

session_start();
error_reporting(E_ALL);

// Путь до корневой директории
$root = '';

// Загрузка вспомогательных библиотек
require 'library/lib_string.php';
require 'library/lib_url.php';

// Загрузка модели
require 'classes/visual/store_url.php';

$urlStore = new \visual\StoreUrl();
$urlStore->getParameters();

require 'classes/model/client.php';

require 'classes/model/horse.php';
require 'classes/model/horse_anatomy.php';
require 'classes/model/horse_social.php';
require 'classes/model/horse_store.php';

// Загрузка тестировочных модулей
require 'classes/simulator/horse_data_set.php';

require 'classes/simulator/client_book.php';
require 'classes/simulator/horse_file_saver.php';
require 'classes/simulator/horse_file_reader.php';
require 'classes/simulator/horse_file_generator_bad.php';
require 'classes/simulator/horse_file_generator_good.php';

// Классы отображения визуальных данных
require 'classes/visual/store_card_decorator.php';
require 'classes/visual/store_showcase.php';
require 'classes/visual/store_navigation.php';

// создаём экземпляр декоратора карточек (для зебра-полос)
$cardDecorator = new \visual\StoreCardDecorator();

// Определяем параметры навигатора в контексте активного раздела или запроса
$storeNavigation = new \visual\StoreNavigation();
$storeNavigation->defineFilter($urlStore);
$storeNavigation->defineFontStyle($urlStore);


// Инициализируем клиента и его предыдующую активность
$client = new \simulator\ClientBook('', $root);
$clientId = $client->id;

if ($urlStore->paramRandomize <> '') {
	$client->reset();
}


// РЕНДЕРИНГ СТРАНИЦЫ

// Начало документа
include("templates/document_start.html");

// Рендерим шапку витрины
include("templates/store_header.html");

// Рендерим навигацию
include("templates/store_navigation.html");

// Создаём экземпляр витрины с количеством элементов на странице
$store = new StoreShowcase(20);


// РЕНДЕРИНГ МАГАЗИНА

//$dataSource = 'database';
$dataSource = 'simulator';

// загружаем витрину из базы данных
if ($dataSource == 'database') {	
	include("control/store_showcase_db.php");	
}
// или загружаем витрину из тестового симулятора
elseif ($dataSource == 'simulator') {	
	include("control/store_showcase_simulator.php");	
}


// Выводим футер магазина с примечаниями
include("templates/store_footer.html");

// Конец документа
include("templates/document_end.html");