<?php
class Doctrine2Plugin extends Object {
	
	public function postSite($action)
	{
		if (in_array('izDoctrine', get_declared_classes())){
			//$manager = Doctrine_Manager::getInstance();
			try {
				$conn = Doctrine_Manager::connection();
				//$conn->flush();
				$conn->close();
				
			} catch (Exception $e) {
				
			}
		}
	}
}
?>
