<?php
class InsertPlugin extends Object
{
	/**
	 * The Layout module will load all the modules in <iz:layout:pos ...
	 * Insert module will be called to get the content after each module is dispatched
	 *
	 * @param izAction $action
	 */
	public function postDispatch( $action )
	{	
		//$action->getContent will call the izAction->getContent
		//the izRender will then be rendered inside izAction->getContent
		$action->setContent( $this->getManager( 'Insert' )->doInsert( $action->getContent() ) );		
	}
    
	/**
	 * This is to replace all the <iz:insert... tag in Layout module, because the Layout module is called before
	 * the Insert module
	 *
	 * @param unknown_type $action
	 */
	public function postSite( $action )
	{		
		$action->setContent( $this->getManager( 'Insert' )->doInsert( $action->getContent() ) );		
	}
	
}
?>