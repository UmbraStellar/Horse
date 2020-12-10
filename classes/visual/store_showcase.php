<?PHP

// Витрина биржи

class StoreShowcase
{
	public $requestCount = 0;
	public $rentCount = 0;
	public $buyCount = 0;	
	public $signedCount = 0;
	public $payedCount = 0;
	public $availableCount = 0;
	
	public $itemsOnPage = 20;	
	
	// Создаём экземпляр
	public function __construct($itemsOnPage = 20)
	{
		$this->itemsOnPage = $itemsOnPage;
	}
	
	
}