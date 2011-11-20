<?php
/**
 * @version $Id: DumbMySql.php
 * @package CapeCode DataObjects for PHP5
 * @author: Richard Allinson
 * @copyright Copyright (C) 2006 Richard Allinson. All rights reserved.
 * @email: dataobjects@capecodehq.com
 * @license: GNU General Public License (GPL)
 */
class DumbMySql extends Object
{
    private $mysqlConnection = null;
    
    private $hostname = null;
    private $username = null;
    private $password = null;
    private $database = null;
    private $prefix = null;
    
    //a log query array
    static private $queries = array();
    
    /**
     * Sets connection values from the provided String.
     * 
     * 'hostname,username,password,database,prefix'
     *
     * @param String $dbString
     */
    public function setDatabaseValues( $dbString )
    {
        $params = explode( ',', $dbString );
        
        $this->setHostname( @$params[0] );
        $this->setUsername( @$params[1] );
        $this->setPassword( @$params[2] );
        $this->setDatabase( @$params[3] );
        $this->setPrefix( @$params[4] );
    }
    
    public function setHostname( $hostname ){ $this->hostname = $hostname; }
    public function setUsername( $username ){ $this->username = $username; }
    public function setPassword( $password ){ $this->password = $password; }
    public function setDatabase( $database ){ $this->database = $database; }
    public function setPrefix( $prefix ){ $this->prefix = $prefix; }
    
    public function getHostname(){ return $this->hostname; }
    public function getUsername(){ return $this->username; }
    public function getPassword(){ return $this->password; }
    public function getDatabase(){ return $this->database; }
    public function getPrefix(){ return $this->prefix; }
    
    /**
     * Closes the Mysql Resource
     *
     */
    public function __destruct()
    {
        if( is_resource( $this->getMysql() ) ) mysql_close( $this->getMysql() );
    }
	
    /**
     * Executes the provided SQL Query and returns either an Array of results
     * or the count of affected rows depending on the value of $affected.
     *
     * @param String $query
     * @param Boolean $affected
     * @return Array | Int
     */
	public function query( $query, $table = null, $affected=false, $raw = false, $id = false )
	{
		$this->getMysql(); // Called to force MySql Connection
		
		//we have $table != null only when we insert record & want to retrieve last inserted id
		if ($table && $id){
			mysql_query("LOCK TABLES apc_forms WRITE");
			mysql_query("SET AUTOCOMMIT = 0");
			mysql_query( $this->buildQuery( $query ) );
			$lastInsertedId = mysql_query("SELECT LAST_INSERT_ID()");
			mysql_query("COMMIT");
			mysql_query("UNLOCK TABLES");
			return $lastInsertedId;
		}else{
			$result = mysql_query( $this->buildQuery( $query ) );
		}		
				
		if( $affected ) return mysql_affected_rows( $this->getMysql() );
		
		if ($raw) return $result;
		
		
		if( !$result ) return array();

		$rows = array();
		
		while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) $rows[] = $row;
		
		return $rows;
	}
	
	/**
	 * Executes the provided SQL Query and returns a count of the affected rows.
	 *
	 * @param String $query
	 * @return Int
	 */
	public function execute( $query, $table = null)
	{	
		return $this->query( $query, $table, true );
	}
	
	/**
	 * This method is a wrapper for mysql_real_escape_string.
	 * The caller should inforce stripslashes as needed.
	 *
	 * @param String $value
	 * @return String
	 */
	public function safeSQL( $value )
	{
	    return mysql_real_escape_string( $value, $this->getMysql() );
	}
	
	/**
	 * Replaces #__ with getPrefix() and stores the Query in self::$queries[].
	 *
	 * @param String $query
	 * @return String
	 */
	private function buildQuery( $query )
	{
	    $query = str_replace( '#__', $this->getPrefix(), $query );
	    
	    self::$queries[microtime()] = $query;
	    
	    return $query;
	}
	
	/**
	 * Returns a MySql Resource or triggers an error on failure.
	 *
	 * @return Resource
	 */
    public function getMysql()
    {
        if( is_resource( $this->mysqlConnection ) ) return $this->mysqlConnection;
        
        $this->mysqlConnection = @mysql_connect( $this->getHostname(), $this->getUsername(), $this->getPassword() )
            or trigger_error( "Cannot connect to MySql '".$this->getHostname()."' in ".get_class( $this )."::connect", E_USER_ERROR );
        
        @mysql_selectdb( $this->getDatabase() )
            or trigger_error( "Cannot open Database '".$this->getDatabase()."' in ".get_class( $this )."::connect", E_USER_ERROR );
        
        return $this->mysqlConnection;
    }
	
    /**
     * Returns all Queries executed by any instance of this Class.
     * Returned Queries are keyed with microtime() at the point of being executed.
     *
     * @return Array
     */
	static public function getQueries(){ return self::$queries; }
}
?>