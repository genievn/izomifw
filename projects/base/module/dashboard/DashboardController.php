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
		$this->getManager('html')->addJs(config('root.url').'libs/extjs/ux/notification/notification.js');
	}
	
	public function cpanel()
	{
		$render = $this->getTemplate('cpanel');
		return $render;
	}
} // END class 
?>