<?php
/**
 * Wfadmin Manager
 *
 * @package WfadminManager
 * @author Thanh H. Nguyen
 */
class WfadminManager extends Object
{
	// =============================
	// = AUTO GENERATED PROPERTIES =
	// =============================
	private $objectValidation = null;
	
	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	public function importConfig($model=null){
		import('apps.base.module.wfadmin.admin.*');
	}
	public function newEmptyObject($model='WfadminModel'){
		$object = object($model);
		return $this->addObjectValidation($object,$model);			
	}
	public function addObjectValidation($object, $model){
		if(!$this->objectValidation){
			$this->objectValidation = object('Validate');
			switch ($model) {
				case 'WfadminModel':
					# $this->objectValidation->insertValidateRule('column_name', 'string', false, 200, 1);
					break;
				
				default:
					# code...
					break;
			}
		}
		return $object->prototype($this->objectValidation);
	}
}
?>
