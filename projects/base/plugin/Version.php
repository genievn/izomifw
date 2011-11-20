<?php
class VersionPlugin extends Object
{
	public function preUpdate( $object, $where )
	{		
		if (method_exists(get_class($object),"preUpdate"))
			$this->getManager(get_class($object))->preUpdate($object, $where);
	}
	public function postUpdate( $object, $where )
	{
		if (method_exists(get_class($object),"postUpdate"))
			$this->getManager(get_class($object))->postUpdate($object, $where);
	}	
}
?>