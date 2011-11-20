<?php
class AccessPlugin extends Object
{
    public function preSite( $action )
    {    	
    	// check if there is any rule in config, default to false    	
        $rule = $this->getManager( 'access' )->hasAccess( $action, false );        
        // let user go if true
        if( $rule === true ) return $action;
        // Bypass for Account Plugin
        if( @$_REQUEST['iz_logout'] || @$_REQUEST['iz_login'] || @$_REQUEST['iz_new'] || @$_REQUEST['iz_lang']) return $action;
        
        // user has no access, check the rule
        if( @$rule['status'] == 'login' && !isset( $rule['module'] ) )        
        {
        	// user must login to access the module        	
            $url = array();
            
            if( $action->getModule() ) $url[] = $action->getModule();
            if( $action->getMethod() ) $url[] = $action->getMethod();
            if( $action->getParams() ) $url[] = implode( '/', $action->getParams() );
            
            $url = implode( '/', $url );
            // append iz_login for the Account Plugin to process
            Event::fire( 'redirect', config( 'root.uri' )."{$url}/?iz_login=1" );
        }        
        
        
        $action->setModule( @$rule['module'] );
        $action->setMethod( @$rule['method'] );
        $action->setParams( @$rule['params'] );
        
        return $action;
    }

	// Check permission before executing any action
	
    public function preDispatch( $action ){
    }
}
?>