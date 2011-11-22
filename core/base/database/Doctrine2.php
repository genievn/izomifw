<?php
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Event\Listeners\MysqlSessionInit,
    DoctrineExtensions\Versionable\Versionable,
    DoctrineExtensions\Versionable\VersionListener;

class izDoctrine extends Object
{
	
	static private $connections = array();
	
	static private $classLoader = null;
	static private $entityLoader = null;
	private $config = null;
	private $cache = null;
	private $em = null;
	private $signature = null;
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function init($dbConfig)
	{	
		$this->signature = md5(serialize($dbConfig));
		/**
		 * Check if the connection exists
		 */
		if (!empty(self::$connections[$this->signature])) return self::$connections[$this->signature];

		if (!self::$entityLoader)
		{
		    self::$entityLoader = new ClassLoader('Entities', config('root.abs'));		
		    self::$entityLoader->register();
		}
		$this->config = new Configuration;
		//$this->config->addCustomStringFunction('YEAR', 'DoctrineExtensions\Query\MySql\Year');
		
		$cache = new ArrayCache;
		$driverImpl = $this->config->newDefaultAnnotationDriver(array(config('root.abs').'Entities'));
		$this->config->setMetadataDriverImpl($driverImpl);
		$this->config->setMetadataCacheImpl($cache);
		$this->config->setQueryCacheImpl($cache);
		// Proxy configuration
		$this->config->setProxyDir(config('root.abs').config('root.proxy_folder') . DIRECTORY_SEPARATOR);
		$this->config->setProxyNamespace('Proxies');
		$this->em = EntityManager::create($dbConfig, $this->config);
		$this->em->getEventManager()->addEventSubscriber(new MysqlSessionInit('utf8', 'utf8_unicode_ci'));
		//$this->em->getEventManager()->addEventSubscriber(new VersionListener()); 
		
		self::$connections[$this->signature] = $this;
		return $this;
	}

	public function getEntityManager()
	{
		
		$conn = self::$connections[$this->signature];		
		
		return $conn->em;
	}

	public function getConfig()
	{
		$conn = self::$connections[$this->signature];
		return $conn->config;
	}
}

?>