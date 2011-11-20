<?php
/**
 * Httpproxy Manager
 *
 * @package HttpproxyManager
 * @author Thanh H. Nguyen
 */
class HttpproxyManager extends Object
{
	// =============================
	// = AUTO GENERATED PROPERTIES =
	// =============================
	private $objectValidation = null;
	
	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	/**
	 * Import the Model Configuration depends on mode (public|admin)
	 *
	 * @param string $model 
	 * @param string $mode 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function importConfig($model=null,$mode="admin"){
		switch ($mode) {
			case 'public':
				import('apps.base.module.httpproxy.public.*');
				break;			
			default:
				import('apps.base.module.httpproxy.admin.*');
				break;
		}		
	}
	/**
	 * Create an empty object of the model
	 *
	 * @param string $model 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function newEmptyObject($model='HttpproxyModel'){
		$object = object($model);
		return $this->addObjectValidation($object,$model);			
	}
	/**
	 * Add validation to object model
	 *
	 * @param string $object 
	 * @param string $model 
	 * @return Object
	 * @author Thanh H. Nguyen
	 */
	public function addObjectValidation($object, $model){
		if(!$this->objectValidation){
			$this->objectValidation = object('Validate');
			switch ($model) {
				case 'HttpproxyModel':
					# $this->objectValidation->insertValidateRule('column_name', 'string', false, 200, 1);
					break;
				
				default:
					# code...
					break;
			}
		}
		return $object->prototype($this->objectValidation);
	}
	// =============================
	// = YOUR OTHER FUNCTIONS HERE =
	// =============================
	
	public function load($url, $format = 'html')
	{
		$curl = object('Curl')->get($url);
		return $curl->get();
	}
}
?>
