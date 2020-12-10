<?PHP

// Класс клиент оперирует данными из текущей карточки в каталоге

namespace model;

Class Client
{
	public $id = '';	
	public $orderId = ''; // last order id (index)
	public $orderSigned = false; // last order sign result
	public $orderPayed = false; // last order payment result
		

	// Инициализация клиента через идентификатор сессии
	public function __construct(string $clientId = '')
	{
		// если $clientId не указан, создаём его из сессии идентификатора сессии
		if (empty($clientId) || ($clientId === '')) {
			$sessionId = session_id();
			if (empty($sessionId) || ($sessionId === '')) return;
			$this->id = md5($sessionId);
		} else {
			// или указывам напрямую
			$this->id = $clientId;
		}				
	}

	
	// Устанавливает статус текущего заказа
	public function setOrderStatus($index, $orderSigned, $orderPayed)
	{
		$this->orderId = $index;
		$this->orderSigned = $orderSigned;
		$this->orderPayed = $orderPayed;
	}

	
	// Сброс состояния
	public function reset()
	{
		$this->orderId = '';
		$this->orderSigned = false;
		$this->orderPayed = false;
	}		
	
}
