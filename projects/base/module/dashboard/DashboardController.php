<?php
/**
 * undocumented class
 *
 * @package default
 * @author Thanh Nguyen
 **/
class DashboardController extends Object 
{
	public function addMeta()
	{
		$this->getManager('html')->addCss(config('root.url').'libs/twitterbootstrap/bootstrap.min.css');
		$this->getManager('izojs')->addLib('extjs');
	}
} // END class 
?>