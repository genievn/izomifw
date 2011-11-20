<?php
class ImagesPlugin extends Object
{
	public function postSite( $action )
	{
	    $action->setContent( $this->getManager( 'Images' )->doImages( $action->getContent() ) );
	}
}
?>