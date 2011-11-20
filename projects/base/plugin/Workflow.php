<?php
/*
class WorkflowPlugin extends Object
{
	public function preUpdate( $object, $where )
	{
		if (method_exists(get_class($object),"preUpdate"))
			$this->getManager(get_class($object))->preUpdate($object, $where);
	}
	public function postUpdate( $object, $where )
	{
		if (method_exists(get_class($object),"postUpdate"))
			$this->getManager(get_class($object))->postUpdate($object, $where);
	}
	public function postInsert($params)
	{
		#izlog($params['id']);
		izlog('[workflow/plugin/postInsert] Reveiving action '.$params['action']);
		$this->getManager('wfengine')->postInsert($params);
	}

	public function beginTransaction($task_id = null)
	{
		izlog('[workflow/plugin/begginTransaction] Receiving beginTransaction event');
		$this->getManager('wfengine')->beginTransaction($task_id);
	}

	public function commitTransaction()
	{
		izlog('[workflow/plugin/commitTransaction] Receiving commitTransaction event');
	    $this->getManager('wfengine')->commitTransaction();
	}

	public function preDispatch($action)
	{

		# Bypass for Account Plugin
        if( @$_REQUEST['iz_logout'] || @$_REQUEST['iz_login'] || @$_REQUEST['iz_new'] || @$_REQUEST['iz_lang']) return $action;

		# form the context
		# $action should be in the form: module/method/any/number/of/extra/parameters/?wfcontext="id=11"


		$context = @$_REQUEST['wfcontext'];
		$task_id = $this->getActionUri($action);
		$params = array(
			"action"=>$task_id
			, "context"=>$context
		);

		$this->getManager('wfengine')->examineWorkflowInstance($params);
	}

	private function getActionUri( $action )
	{
		if( !is_a( $action, 'izAction' ) ) return;

		$uri = '';

		if( $action->getModule() ) $uri.= $action->getModule().'/';
		if( $action->getMethod() ) $uri.= $action->getMethod().'/';
		if( is_array( $action->getParams() ) ) $uri.= implode( '/', $action->getParams() );

		return $uri;
	}
}*/
?>