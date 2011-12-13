<?php
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Event\Listeners\MysqlSessionInit;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ApcCache;

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
		global $ds;
		$this->signature = md5(serialize($dbConfig));
		/**
		 * Check if the connection exists
		 */
		if (!empty(self::$connections[$this->signature])) return self::$connections[$this->signature];

		if (!self::$entityLoader)
		{
		    self::$entityLoader = new ClassLoader('Entity', config('root.abs'));		
		    self::$entityLoader->register();
		}
		$this->config = new Configuration;
		// setting cache
		if(ENVIRONMENT == 'development')
            // set up simple array caching for development mode
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        else
            // set up caching with APC for production mode
            $cache = new \Doctrine\Common\Cache\ApcCache;

		//$this->config->addCustomStringFunction('YEAR', 'DoctrineExtensions\Query\MySql\Year');
		/*
		$cache = new ArrayCache;
		$driverImpl = $this->config->newDefaultAnnotationDriver(array(
			config('root.abs').'Entity',
			config('root.abs').'libs'.$ds.'gedmo'.$ds.'lib'.$ds.'Gedmo'.$ds.'Translatable'.$ds.'Entity'
		));
		$this->config->setMetadataDriverImpl($driverImpl);
		$this->config->setMetadataCacheImpl($cache);
		*/
		//$reader = new \Doctrine\Common\Annotations\AnnotationReader();
		$reader = new CachedReader(
		    new AnnotationReader(),
		    $cache,
		    $debug = true
		);
		AnnotationRegistry::registerFile(realpath(__DIR__."/../../../libs/doctrine2/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php"));
		AnnotationRegistry::registerAutoloadNamespace(
		    'Gedmo\\Mapping\\Annotation',
		    realpath(__DIR__.'/../../../libs/gedmo/lib')
		);
		$chain = new \Doctrine\ORM\Mapping\Driver\DriverChain();
		$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, array(
		    realpath(__DIR__."/../../../Entity"),
		    realpath(__DIR__."/../../../libs/gedmo/lib/Gedmo/Translatable/Entity"),
			realpath(__DIR__."/../../../libs/gedmo/lib/Gedmo/Loggable/Entity"),
			realpath(__DIR__."/../../../libs/gedmo/lib/Gedmo/Tree/Entity")
		));
		// drivers
		$chain->addDriver($annotationDriver, 'Gedmo\\Translatable\\Entity');
		$chain->addDriver($annotationDriver, 'Gedmo\\Loggable\\Entity');
		$chain->addDriver($annotationDriver, 'Gedmo\\Tree\\Entity');
		$chain->addDriver($annotationDriver, 'Entity');
		$this->config->setMetadataCacheImpl($cache);
		$this->config->setMetadataDriverImpl($chain);		
		$this->config->setQueryCacheImpl($cache);
		// Proxy configuration
		$this->config->setProxyDir(config('root.abs').config('root.proxy_folder') . DIRECTORY_SEPARATOR);
		$this->config->setProxyNamespace('Proxies');
		// auto-generate proxy classes if we are in development mode
		$this->config->setAutoGenerateProxyClasses(ENVIRONMENT == 'development');
		// Event Manager
		$evm = new \Doctrine\Common\EventManager();
		// timestampable
		$evm->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
		// tree
		$evm->addEventSubscriber(new \Gedmo\Tree\TreeListener());
		// sluggable
		$evm->addEventSubscriber(new \Gedmo\Sluggable\SluggableListener());
		// Tranlatable Listener
		$translatableListener = new \Gedmo\Translatable\TranslationListener();
		$translatableListener->setTranslatableLocale(config('root.default_lang'));		
		$evm->addEventSubscriber($translatableListener);
		
		$this->em = EntityManager::create($dbConfig, $this->config, $evm);
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