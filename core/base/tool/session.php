<?php
/**
 * @version $Id: DataSession.php
 * @package CapeCode DataObjects for PHP5
 * @author: Richard Allinson
 * @copyright Copyright (C) 2006 Richard Allinson. All rights reserved.
 * @email: dataobjects@capecodehq.com
 * @license: GNU General Public License (GPL)
 */
define( 'IZ_SESSION_ACTIVE', 'IZ_SESSION_ACTIVE' );

class Session extends Object
{
    static private $stored = false;
    
    private $instance = 'object-session-root';
    
    public function setCookieControls( $lifeTime, $path='/', $domain=null, $secure=null )
    {
        if( @$_SESSION[IZ_SESSION_ACTIVE] )
            trigger_error( 'Session ERROR, cannot set Cookie Controls after "Session::store()" has been called', E_USER_ERROR );
        
        session_set_cookie_params( $lifeTime, $path, $domain, $secure );
    }
    
    public function instance( $instance=null )
    {
        if( !$instance ) return $this->instance;
        
        $this->instance = $instance;
        
        return $this;
    }
    
    public function store()
    {
        session_write_close();
        
        self::$stored = true;
        
        $_SESSION[IZ_SESSION_ACTIVE] = 0;
        
        return $this;
    }
    
    public function kill( $all=false )
    {
        $this->sessionStart();
        
        unset( $_SESSION[$this->instance()] );
        
        if( !$all ) return;
        
        $_SESSION = array();
        
        if( isset( $_COOKIE[$this->getSessionName()] ) )
            setcookie( $this->getSessionName(), '', time()-42000, '/' );
        
        unset( $_REQUEST[$this->getSessionName()] );
        unset( $_COOKIE[$this->getSessionName()] );
        unset( $_POST[$this->getSessionName()] );
        unset( $_GET[$this->getSessionName()] );
        
        session_unset();
        session_destroy();        
        
        return $this;
    }
    
    public function __set( $key, $value )
    {
        $this->sessionStart();
        
        $_SESSION[$this->instance()][$key] = $value;
        
        return $this;
    }
    
    public function __get( $key )
    {
        $this->sessionStart();
        
        if( isset( $_SESSION[$this->instance()] ) && isset( $_SESSION[$this->instance()][$key] ) )
            return $_SESSION[$this->instance()][$key];
        
        return parent::__get( $key );
    }
    
    public function hasOwnProperty( $key )
    {
        return isset( $_SESSION[$this->instance()] ) && isset( $_SESSION[$this->instance()][$key] );
    }
    
    public function setSessionHandler( $handler )
    {
        if( !is_subclass_of( $handler, 'SessionHandlerInterface' ) )
            trigger_error( 'Session ERROR, "Session Handler" must use SessionHandlerInterface', E_USER_ERROR );
        
        if( $this->detectSession() )
            trigger_error( 'Session ERROR, cannot set Session Handler after Session has been started', E_USER_ERROR );
        
        return session_set_save_handler(
            $handler->open(),
            $handler->close(),
            $handler->read(),
            $handler->write(),
            $handler->destory(),
            $handler->gc()
        );
    }
    
    public function setSessionId( $id=null )
    {
        if( $this->detectSession() )
            return $this->regenerateSessionId( $id );
        
        if( $id )
            session_id( $id );
        else
            session_id( $this->makeSessionId() );
        
        return $this;
    }
    
    private function regenerateSessionId( $id=null )
    {
        if( $this->detectSession() === false )
            trigger_error( 'Session ERROR, cannot Regenerate a Session Id before the Session has been started', E_USER_ERROR );
        
        $_SESSION_COPY = $_SESSION;
        
        $this->kill( true );
        $this->sessionStart( $id );
        
        $_SESSION = $_SESSION_COPY;
        
        return $this;
    }
    
    public function setSessionName( $name )
    {
        if( $this->detectSession() )
            trigger_error( 'Session ERROR, cannot set Session Name after Session has been started', E_USER_ERROR );
        
        session_name( $name );
        
        return $this;
    }
    
    public function getSessionName()
    {
        return session_name();
    }
    
    public function getSessionId()
    {
        return session_id();
    }
    
    public function sessionStart( $id=null )
    {
        if( @$_SESSION[IZ_SESSION_ACTIVE] ) return;
        
        if( $this->detectSession() === false )
            $this->setSessionId( $id );
        
        // Removed as session did not update lifetime + client vs server time differance
        //$this->setCookieControls( 10*60 ); // 10 minutes
        session_cache_limiter( 'nocache' );
        session_start();
        
        // Send modified header for IE 6.0 Security Policy
		header( 'P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"' );
		
		// Mark the Session as ACTIVE
		$_SESSION[IZ_SESSION_ACTIVE] = 1;
		
		return $this;
    }
    
    private function detectSession()
    {
        if( isset( $_COOKIE[$this->getSessionName()] ) )
        {
            return $_COOKIE[$this->getSessionName()];
        }            
        
        return false;
    }
    
    private function makeSessionId()
    {
        return md5( @$_SERVER['HTTP_USER_AGENT'].uniqid(rand()).microtime() );
    }
}

interface SessionHandlerInterface
{
    public function open( $save_path, $session_name );
    public function close();
    public function read( $id );
    public function write( $id, $sess_data );
    public function destory( $id );
    public function gc( $maxlifetime );
}
?>