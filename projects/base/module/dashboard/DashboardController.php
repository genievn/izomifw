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
		izlog('meta called');
		$this->getManager('izojs')->addLib('extjs');
	}
} // END class 
?>