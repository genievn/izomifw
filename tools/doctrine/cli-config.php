<?php
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

require_once '../../libs/doctrine2/lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', realpath(__DIR__ . '/../../libs/doctrine2/lib'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', realpath(__DIR__ . '/../../libs/doctrine2/lib/vendor/doctrine-dbal/lib'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', realpath(__DIR__ . '/../../libs/doctrine2/lib/vendor/doctrine-common/lib'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Symfony', realpath(__DIR__ . '/../../libs/doctrine2/lib/vendor'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Gedmo', realpath(__DIR__ . '/../../libs/gedmo/lib'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Entity', realpath(__DIR__ . '/../../'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Proxies', realpath(__DIR__ . '/../../'));
$classLoader->register();


$config = new \Doctrine\ORM\Configuration();

$reader = new \Doctrine\Common\Annotations\AnnotationReader();
AnnotationRegistry::registerFile(realpath(__DIR__."/../../libs/doctrine2/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php"));
AnnotationRegistry::registerAutoloadNamespace(
    'Gedmo\\Mapping\\Annotation',
    realpath(__DIR__.'/../../libs/gedmo/lib')
);
$chain = new \Doctrine\ORM\Mapping\Driver\DriverChain();
$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, array(
    realpath(__DIR__."/../../Entity"),
    realpath(__DIR__."/../../libs/gedmo/lib/Gedmo/Translatable/Entity"),
	realpath(__DIR__."/../../libs/gedmo/lib/Gedmo/Loggable/Entity"),
	realpath(__DIR__."/../../libs/gedmo/lib/Gedmo/Tree/Entity")
));
// drivers
$chain->addDriver($annotationDriver, 'Gedmo\\Translatable\\Entity');
$chain->addDriver($annotationDriver, 'Gedmo\\Loggable\\Entity');
$chain->addDriver($annotationDriver, 'Gedmo\\Tree\\Entity');
//$chain->addDriver($annotationDriver, 'Entity\\Base');
$chain->addDriver($annotationDriver, 'Entity');

$config->setMetadataDriverImpl($chain);


/*
$driverImpl = $config->newDefaultAnnotationDriver(array(
	realpath(__DIR__."/../../Entity"),
	realpath(__DIR__."/../../libs/gedmo/lib/Gedmo/Translatable/Entity"),
	realpath(__DIR__."/../../libs/gedmo/lib/Gedmo/Loggable/Entity"),
	realpath(__DIR__."/../../libs/gedmo/lib/Gedmo/Tree/Entity")
));
$config->setMetadataDriverImpl($driverImpl);
*/

$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
$config->setProxyDir(realpath(__DIR__ . '/../../Proxies'));
$config->setProxyNamespace('Proxies');
$config->setAutoGenerateProxyClasses(true);

$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'izomifw',
    'user' => 'izomi',
    'password' => 'izomi',
	'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'
);

// Event Manager
$evm = new \Doctrine\Common\EventManager();
// Tranlatable Listener
$translatableListener = new \Gedmo\Translatable\TranslationListener();
$translatableListener->setTranslatableLocale('en-US');		
$evm->addEventSubscriber($translatableListener);

$tree = new \Gedmo\Tree\TreeListener;
$tree->setAnnotationReader($reader);
$evm->addEventSubscriber($tree);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config, $evm);

$helpers = array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
);