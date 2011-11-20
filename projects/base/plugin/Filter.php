<?php
class FilterPlugin extends Object
{
	public function startUp( $action )
	{
	    $this->getManager( 'Filter' )->filterRequest();
	}
}
?>