<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
class AboutController extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function about()
	{
		$render = $this->getTemplate('about');
		return $render;
	}
} // END class 
?>