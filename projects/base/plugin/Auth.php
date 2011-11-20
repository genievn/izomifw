<?php
class AuthPlugin extends Object
{
    public function preSite( $action )
    {
        $this->actions( $action );
    }
    
    private function actions( $action )
    {    	
        /**
         * User login params
         */        
        $remember = @$_REQUEST['iz_remember'];
        $username = @$_REQUEST['iz_user']; // Plain Text
        $password = @$_REQUEST['iz_spass']; // MD5 hash
        if( empty( $password ) ) $password = md5( @$_REQUEST['iz_pass'] ); // Plain Text
        
        /**
         * User action params
         */
        $forget = @$_REQUEST['iz_forget'];
        $logout = @$_REQUEST['iz_logout'];
        $login = @$_REQUEST['iz_login'];
        $new = @$_REQUEST['iz_new'];
        /**
         * Used to Login a User.
         * Simply add iz_user={string}&iz_pass={string} to a request
         * optionally add iz_remember=1 to Remember the user.
         */
        if( $username && $password || $login )
        {
            if( !$this->getManager( 'auth' )->loginAccount( $username, $password, $remember ) )
            {
                $nextAction = $action->copy();				//Remember action to return back after login
                $action->setModule( 'auth' );
                $action->setMethod( 'loginForm' );
                $action->setParams( array( $username, $remember, $nextAction ) );
            }
            
        }
        
        /**
         * Used to log out a User.
         * Simply add iz_logout=1 to a request
         */
        if( $logout )
        {
            $this->getManager( 'auth' )->logoutAccount();            
            Event::fire( 'redirect', config('root.uri') );
        }
        
        /**
         * Used to remove the Remember Me cookie.
         * Simply add iz_forget=1 to a request
         */
        if( $forget ) $this->getManager( 'auth' )->forgetAccount();
        
        /**
         * Used to create a New Account
         * Simply add co_new=1 to a request
         */
        if( $new )
        {
            $nextAction = $action->copy();
            $action->setModule( 'auth' );
            $action->setMethod( 'create' );
            $action->setParams( array( $nextAction ) );
        }
    }
}
?>