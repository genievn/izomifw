<?php
/**
 * The layout plugin is loaded later, initally there is a place holder for it
 *
 */

class LayoutPlugin extends Object
{
	public function postSite( $action )
	{			
		$action->setContent( $this->getManager( 'Layout' )->doLayout( $action ) );				
	}
}
?>