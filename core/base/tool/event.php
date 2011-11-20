<?php
define( 'EVENTS_CALLED', '__events_called__');

class Event extends Object
{
    /**
     * @var Array
     */
    static private $listeners = array();
    static private $history = array( EVENTS_CALLED => array() );
    static private $map = array();
    
    /**
     * Add an Object that will listen for an event
     *
     * @param Object $listener
     * @return Boolean
     */
    public function addListener( $listener, $listenerId )
    {
        $listenerId = intval( $listenerId );
        
        self::$listeners[$listenerId] = $listener;
        
        if( !isset( self::$history[$listener->toString()] ) )
            self::$history[$listener->toString()] = array();
        
        ksort( self::$listeners );
        return true;
    }
    
    /**
     * Fires an Event
     *
     */
    static public function fire()
    {
        $args = func_get_args();
        $method = @$args[0];
        $args = array_slice( $args, 1 );
        
        self::$history[EVENTS_CALLED][] = $method;
        
        $postion = 0;
        
        while( $postion < count( self::$listeners ) )
        {
            $keys = array_keys( self::$listeners );
        
            if( method_exists( self::$listeners[$keys[$postion]], $method ) )
            {
                self::$history[self::$listeners[$keys[$postion]]->toString()][] = $method;
                call_user_func_array( array( self::$listeners[$keys[$postion]], $method ), $args );
            }
            
            $postion++;
        }
    }
    
    static public function getHistory()
    {
        return self::$history;
    }
    
    static public function getEventMap()
    {
        return self::$map;
    }
}
?>