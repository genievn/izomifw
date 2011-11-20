<?php
class DebugPlugin extends Object
{
    public function startDebug()
    {
        $this->setStartTime( microtime( true ) );
    }
    
    /**
     * @param coAction $action
     */
    public function closeDebug( $action )
    {
        $this->setFinishTime( microtime( true ) );        
        if( config( 'root.debug', false ) )
            $action->setContent( $action->getContent().$this->showDebug() );
    }
    
    private function showDebug()
    {
        ob_start();
        
        $executionTime =  $this->getFinishTime() - $this->getStartTime();
        
        echo '<hr /><h3 style="text-align: left; color: green;">Site Debug Start: '.date( 'l jS F Y @ g:i:s a' ).'</h3><hr />';
        
        if( config( 'root.max_execution_warn' ) < $executionTime )
            echo '<h2 style="color: red;">Max Execution Time Warning</h2><br />';
        
        echo "<pre style=\"text-align: left; font-size: 1em; line-height: 1em;\">";
        
        echo "Total Execution Time\n";
        echo "(\n    [Seconds] => ".$executionTime."\n)\n\n";
        
        try{
        echo "Account\n";
        //echo "(\n    [User Status] => Logged ". ( $this->getManager( 'account' )->isLoggedIn() ? "In":"Out" )."\n)\n\n";        	
        }catch(Exception $e){
        	echo "<span style=\"color:red;\">$e->getMessage()</span>";
        }
        
        echo "Session";
        print_r( @$_SESSION );
        
        echo "\nGet";
        print_r( @$_GET );
        
        echo "\nPost";
        print_r( @$_POST );
        
        echo "\nFiles";
        print_r( @$_FILES );
        
        echo "\nCookie";
        print_r( @$_COOKIE );
        
        if( class_exists( 'DumbMySql' ) )
        {
            echo "\nDumbMySql";
            print_r( DumbMySql::getQueries() );
        }
        
        if( class_exists( 'Config' ) )
        {
            echo "\nConfigAccessCount\n";
            echo "(\n    [Access Count] => ". ( Config::getAccessCount() )."\n)\n";
            
            echo "\nConfig";
            print_r( Config::getConfig() );
            
            echo "\nConfigFiles";
            print_r( Config::getHistory() );
        }
        
        if( class_exists( 'Import' ) )
        {
            echo "\nImport";
            print_r( Import::getHistory() );
        }
        
        if( class_exists( 'Locale' ) )
        {
            echo "\nLocale";
            print_r( izLocale::getHistory() );
        }
        
        if( class_exists( 'Event' ) )
        {
            echo "\nEvent";
            print_r( Event::getHistory() );
            echo "\nEventMap";
            print_r( Event::getEventMap() );
        }
        
        if( class_exists( 'Replace' ) )
        {
            $count = array( 'Tag'=>Replace::$tagReplaceCount, 'Element'=>Replace::$elementReplaceCount );
            echo "\nReplace";
            print_r( $count );
        }
        
        echo "</pre>";
        echo '<hr /><h3 style="color: green;">Site Debug End</h3><hr />';
        
        return ob_get_clean();
    }
}
?>