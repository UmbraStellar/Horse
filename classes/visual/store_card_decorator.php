<?PHP

// Реализация чередования светлой и тёмной полосы на карточке средствами PHP

namespace visual;

class StoreCardDecorator
{
	private	$shade;
	private	$columnIndex;

	
	// Создаём экземпляр и сбрасываем в дефолтное состояние
	public function __construct() {
		$this->reset();
	}
	
	
	// Сброс переменных перед началом новой карточки
	public function reset()
	{
		$this->shade = 0;
		$this->columnIndex = 0;
	}

	// Возвращает текущий субкласс оттенка
	public function subClass()
	{		
		$this->columnIndex++;
		
		// Цвет строки меняется каждые два столбца
		if ($this->columnIndex > 2) {
			$this->columnIndex = 1;
			$this->shade = 1 - $this->shade;
		}		
		
		// Возвращает название субкласса для темных полос определённый в CSS
		if ($this->shade == 0) {
			return '';
		} else {
			return 'td-dark';
		}
	}
}