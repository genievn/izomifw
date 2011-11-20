<?php
/**
 * Wfadmin Controller
 *
 * @package WfadminController
 * @author Thanh H. Nguyen
 */
class WfadminController extends Object
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
}
?>