<?php
/**
 * @version $Id: DumbRecord.php
 * @package CapeCode DataObjects for PHP5
 * @author: Richard Allinson
 * @copyright Copyright (C) 2006 Richard Allinson. All rights reserved.
 * @email: dataobjects@capecodehq.com
 * @license: GNU General Public License (GPL)
 */
class DumbRecord extends Object
{
    private $canCreate = false;
    protected $mysql = null;
    private $debugMessages = array();
    private $prefix = null;
    
    public function __call( $method, $args=array() )
    {
        $key = strtolower( substr( $method, 3 ) );
        
        switch( substr( $method, 0, 6 ) )
        {
            case 'select':
                return call_user_func_array( array( $this, 'selectRecords' ), $args );
            case 'counts':
                return call_user_func_array( array( $this, 'countsRecords' ), $args );
            case 'choose':
                return call_user_func_array( array( $this, 'chooseRecord' ), $args );
            case 'insert':
                return call_user_func_array( array( $this, 'insertRecord' ), $args );
            case 'update':
                return call_user_func_array( array( $this, 'updateRecord' ), $args );
            case 'delete':
                return call_user_func_array( array( $this, 'deleteRecord' ), $args );
        }
        
        if( $this->mysql )
        {
            switch( $method )
            {
                case 'query': case 'execute': case 'safeSQL':
                    return call_user_func_array( array( $this->mysql, $method ), $args );
            }
        }
        
        return parent::__call( $method, $args );
    }
    
    public function setMySql( $mySql )
    {
        if( !is_object( $mySql ) || !method_exists( $mySql, 'query' ) )
            trigger_error( "MySql Object must implement a 'query( \$sql_string )' method.", E_USER_ERROR );
        
        if( !is_object( $mySql ) || !method_exists( $mySql, 'execute' ) )
            trigger_error( "MySql Object must implement an 'execute( \$sql_string )' method.", E_USER_ERROR );
        
        if( !is_object( $mySql ) || !method_exists( $mySql, 'safeSQL' ) )
            trigger_error( "MySql Object must implement a 'safeSQL( \$string )' method.", E_USER_ERROR );
        
        $this->mysql = $mySql;
    }
    
    protected function mirrorDataObject( $dataObject )
    {    	
        if( !is_a( $dataObject, 'Object' ) )
            trigger_error( "Argument must be of type 'DataObject' in ".get_class( $this )."::".$method, E_USER_ERROR );
        
        //if it's in production, then canCreate should be false;
        if( !$this->canCreate() ) return false;
        
        try
        {
            if( !$this->isTable( $this->getTableName( $dataObject ) ) )
                if( $this->createTable( $dataObject ) )
                    $this->logDebug( 'Error Creating Table '.$this->getTableName( $dataObject ) );
            
            $this->createColumns( $dataObject );
            
            return true;
        }
        catch( Exception $error )
        {
            return false;
        }
    }
    /**
     * Check if the table is already there in the database
     *
     * @param unknown_type $table
     * @return unknown
     */
    
    private function isTable( $table )
    {
        $tables = $this->query( "SHOW TABLES" );
        
        foreach( $tables as $data )
            foreach( $data as $name )
                if( $name == $table )
                    return true;
        
        return false;
    }
    
    /**
     * Create a table with all the properties of the object
     *
     * @param unknown_type $dataObject
     * @return unknown
     */
    private function createTable( $dataObject )
    {
        $table = $this->getTableName( $dataObject );
        
        $queryArray = array();
        
        foreach( $dataObject->properties() as $key => $value )
            $queryArray[] = "$key VARCHAR(255)";
        
        $query = implode( ', ', $queryArray );
        
        return $this->execute( "CREATE TABLE $table ($query)" );
    }
    
    /**
     * Create / Add more columns to existing table
     *
     * @param unknown_type $dataObject
     * @return unknown
     */
    private function createColumns( $dataObject )
    {
        $table = $this->getTableName( $dataObject );
        
        $dataColumns = $dataObject->properties();
        $curColumns = $this->query( "SHOW COLUMNS FROM $table" );
        $exsColumns = array();
        $newColumns = array();
        
        foreach( $curColumns as $curColumn )
            $exsColumns[$curColumn['Field']] = $curColumn['Field'];
        
        foreach( $dataColumns as $key => $value )
            if( !isset( $exsColumns[$key] ) )
                $newColumns[] = "ADD $key VARCHAR(255)";
        
        if( !count( $newColumns ) ) return true;
        
        $query = implode( ', ', $newColumns );
        
        return $this->execute( "ALTER TABLE $table $query" );
    }
    
    protected function getTableName( $dataObject )
    {
        return $this->getPrefix().strtolower( get_class( $dataObject ).'s' );
    }
    
    public function insertRecord( $dataObject )
    {    	
        if( !$this->mirrorDataObject( $dataObject ) ) $this->logDebug( 'DumbRecord is dumb...' );
        
        $queryArray = array();
        
        foreach( $dataObject->properties() as $key => $value )
        {
            $keysArray[] = $key;
            $valuesArray[] = $this->makeSafeSQL( $value );
        }
        
        $table = $this->getTableName( $dataObject );
        $keys = implode( ', ', $keysArray );
        $values = implode( ', ', $valuesArray );        
        return $this->execute( "INSERT INTO $table($keys) VALUES($values)", $table );
    }
    
    public function updateRecord( $dataObject, $where=array() )
    {
    	//version & workflow testing
    	Event::fire('preUpdate',$dataObject, $where);
    	
        if( !$this->mirrorDataObject( $dataObject ) ) $this->logDebug( 'DumbRecord is dumb...' );
        
        $queryArray = array();
        
        foreach( $dataObject->properties() as $key => $value )
            $queryArray[] = $key." = ".$this->makeSafeSQL( $value );

        $key = array_shift( $queryArray );
        $table = $this->getTableName( $dataObject );
        $query = implode( ', ', $queryArray );
        
        if( count( $where ) ) $key.= ' AND '.implode( ' AND ', $where );
		//echo "UPDATE $table SET $query WHERE $key";
        $bool = $this->execute( "UPDATE $table SET $query WHERE $key" );
        Event::fire('postUpdate', $dataObject, $where);
        return $bool;
    }
    
    public function deleteRecord( $dataObject )
    {
        if( !$this->mirrorDataObject( $dataObject ) ) $this->logDebug( 'DumbRecord is dumb...' );
        
        $queryArray = array();
        
        foreach( $dataObject->properties() as $key => $value )
            if( $value && $value != 'NULL' )
                $queryArray[] = $key." = ".$this->makeSafeSQL( $value );
        
        $table = $this->getTableName( $dataObject );
        $query = implode( ' AND ', $queryArray );
        
        return $this->execute( "DELETE FROM $table WHERE $query" );
    }
    
    public function chooseRecord( $dataObject, $where=array() )
    {
        $row = $this->selectRecord( $dataObject, 0, 1, array(), $where );
        
        if( count( $row ) == 1 ) return $row[0];
        
        return $this->getDumbRecordFromArray( array(), get_class( $dataObject ) );
    }
    
    public function selectRecords( $dataObject, $start=0, $limit=null, $order=array(), $where=array() )
    {
        if( !$this->mirrorDataObject( $dataObject ) ) $this->logDebug( 'DumbRecord is dumb...' );
        
        $query = $this->createQuery( $dataObject, $start, $limit, $order, $where );
        
        //if (get_class($dataObject)=="article")
        //	echo $query;
        
        return $this->getDumbRecordsFromArray( $this->query( $query ), get_class( $dataObject ) );
    }
    
    public function countsRecords( $dataObject, $where=array() )
    {
        if( !$this->mirrorDataObject( $dataObject ) ) $this->logDebug( 'DumbRecord is dumb...' );
        
        $query = $this->createQuery( $dataObject, 0, null, array(), $where, true );
        
        $result = $this->query( $query );
        
        if( isset( $result[0] ) && isset( $result[0]['count(*)'] ) )
            return $result[0]['count(*)'];
        
        return 0;
    }
    
    private function createQuery( $dataObject, $start=0, $limit=null, $order=array(), $where=array(), $total=false )
    {
        $queryArray = array();
        
        foreach( $dataObject->properties() as $key => $value )
        {
            $value = $this->makeSafeSQL( $value );
            
            if( $value == 'NULL' )
                $queryArray[] = $key." IS NULL";
            else
                $queryArray[] = $key." = ".$value;
        }
        
        $table = $this->getTableName( $dataObject );
        
        $colums = "*";
        if( $total ) $colums = "count(*)";
        
        $query = "SELECT $colums FROM $table";
        
        $whereList = implode( ' AND ', $queryArray );
        if( $whereList ) $query = "$query WHERE $whereList";
        
        $where = implode( ' AND ', $where );
        if( $whereList && $where )
            $query = "$query AND $where";
        elseif( $where )
            $query = "$query WHERE $where";
        
        $order = implode( ', ', $order );
        if( $order ) $query = "$query ORDER BY $order";
        
        if( $start && $limit )
            $query = "$query LIMIT $start, $limit";
        elseif( $limit )
            $query = "$query LIMIT $limit";
            
        return $query;
    }
    
    public function makeSafeSQL( $value )
	{
	    if( strlen( $value ) === 0 ) return 'NULL';
	    
	    if( is_numeric( $value ) && $value[0] != 0  ) return $value;
	    
	    return "'".$this->safeSQL( $value )."'";
	}
    
    public function getDumbRecordsFromArray( $array, $class )
    {
        $records = array();
        
        foreach( $array as $row )
            $records[] = $this->getDumbRecordFromArray( $row, $class );
            
        return $records;
    }
    
    
    public function getDumbRecordFromArray( $array, $class )
    {
        return new $class( $array );
    }
    
    public function setPrefix( $prefix ){ $this->prefix = $prefix; }
    
    public function getPrefix(){ return $this->prefix; }
    
    public function getDebugLog(){ return $this->debugMessages; }
    
    public function setCanCreate( $canCreate ){ $this->canCreate = $canCreate; }
    
    private function canCreate(){ return $this->canCreate; }
    
    private function logDebug( $message ){ $this->debugMessages[] = $message; }
}
?>