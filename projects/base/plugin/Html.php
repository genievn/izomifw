<?php
class HtmlPlugin extends Object
{
	public function postSite( $action )
	{
	    $action->setObject( $this->getManager( 'Html' )->doHtml( $action->getContent() ) );
	    $action->setContent( '' );
	}
}
?>