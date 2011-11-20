<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
import('core.base.database.orm.doctrine.lib.Doctrine');
spl_autoload_register ( array ( 'Doctrine' , 'autoload' ) ) ;

class izDoctrine extends Object
{
	private $dbString = null;
	private static $doctrineConnection = null;
	private $profiler = null;
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function init($dbString)
	{
		$this->dbString = $dbString;
		$this->profiler = new Doctrine_Connection_Profiler();
		self::$doctrineConnection = Doctrine_Manager::connection($dbString);
		self::$doctrineConnection->setListener($this->profiler);
		Doctrine_Manager::getInstance()->setAttribute('model_loading', 'conservative');
		self::$doctrineConnection->setCharset('UTF8');

		return $this;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getConnection()
	{
		return self::$doctrineConnection;
	}
} // END class 
?>