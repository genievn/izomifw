<?php
/**
 * Httpproxy Controller
 *
 * @package HttpproxyController
 * @author Thanh H. Nguyen
 */
class HttpproxyController extends Object
{
	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	public function defaultCall(){
		$render = $this->getTemplate('default');
		return $render;
	}
	public function admin($model = null){
		$render = $this->getTemplate('admin');
		$render->setModel($model);
		return $render;
	}
	
	// =============================
	// = YOUR OTHER FUNCTIONS HERE =
	// =============================
	
	public function load($format = 'json', $url = null)
	{
		if (!$url)
			$url = @$_REQUEST['url'];
		switch ($format) {
			case 'json':
				print $this->getManager('httpproxy')->load($url);
				break;
			
			default:
				# code...
				break;
		}
		 
	}
}
?>
