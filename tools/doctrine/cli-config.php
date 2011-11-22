<?php

require_once '../../libs/doctrine2/lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', realpath(__DIR__ . '/../../libs/doctrine2/lib'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', realpath(__DIR__ . '/../../libs/doctrine2/lib/vendor/doctrine-dbal/lib'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', realpath(__DIR__ . '/../../libs/doctrine2/lib/vendor/doctrine-common/lib'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Symfony', realpath(__DIR__ . '/../../libs/doctrine2/lib/vendor'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__);
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
$classLoader->register();

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
$driverImpl = $config->newDefaultAnnotationDriver(array(realpath(__DIR__."/../../Entities")));
$config->setMetadataDriverImpl($driverImpl);

$config->setProxyDir(realpath(__DIR__ . '/../../Proxies'));
$config->setProxyNamespace('Proxies');

$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'izomifw',
    'user' => 'izomi',
    'password' => 'izomi',
	'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$helpers = array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
);