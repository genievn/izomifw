<?php
#
# This configuration file is loaded by the Doctrine CLI whenever you execute
# a task. A CLI configuration file usually initializes two local variables:
#
# $em - An EntityManager instance that the CLI tasks should use.
# $globalArguments - An array of default command line arguments that are passed to all
#                    CLI tasks automatically when an argument is not specifically set on
#                    the command line.
#
# You can create several CLI configuration files with different names, for different databases.
# Every CLI task recognizes the --config=<path> option where you can specify the configuration
# file to use for a particular task. If this option is not given, the CLI looks for a file
# named "cli-config.php" (this one) in the same directory and uses that by default.
#

require_once '../../libs/doctrine2/lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Entities', realpath(__DIR__.'/../../'));
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Proxies', realpath(__DIR__.'../../'));
$classLoader->register();

$config = new \Doctrine\ORM\Configuration();
$driverImpl = $config->newDefaultAnnotationDriver(array(realpath(__DIR__."/../../Entities")));
$config->setMetadataDriverImpl($driverImpl);
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
$config->setProxyDir(realpath(__DIR__ . '/../../Proxies'));
$config->setProxyNamespace('Proxies');

$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'simplecms',
    'user' => 'simplecms',
    'password' => 'simplecms'
);
/*
$connectionOptions = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'izomicmsnew',
    'user' => 'izomicmsnew',
    'password' => 'izomicmsnew'
);*/

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

#$configuration = new \Doctrine\Common\CLI\Configuration();
#$configuration->setAttribute('em', $em);
#$configuration->setAttribute('globalArguments', array(
#    'class-dir' => realpath(__DIR__.'/../../Entities')
#));
$helperSet = new \Symfony\Components\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
