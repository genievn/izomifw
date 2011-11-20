<?php
class SiteController extends Object
{
	public function defaultCall(){
		$render=$this->getTemplate('default');
		return $render;
	}
	public function admin($model = null)
	{
		# code...
		$render = $this->getTemplate('admin');
		$render->setModel($model);
		return $render;
	}
	public function test()
	{
		$manager = $this->getManager('site');
		$manager->test();
	}
}
?>