<?php
use Doctrine\Common\ClassLoader;
 
global $abs,$ds; 
require ($abs.'libs'.$ds.'doctrine-orm'.$ds.'Doctrine'.$ds.'Common'.$ds.'ClassLoader.php');
$classLoader = new ClassLoader('Doctrine\Common',$abs.'libs'.$ds.'doctrine-orm');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\DBAL',$abs.'libs'.$ds.'doctrine-orm');
$classLoader->register();
$classLoader = new ClassLoader('Doctrine\ORM',$abs.'libs'.$ds.'doctrine-orm');
$classLoader->register();
$classLoader = new ClassLoader("DoctrineExtensions", $abs.'libs'.$ds.'extensions');
$classLoader->register();
$classLoader = new ClassLoader("Entities", config('root.abs'));
$classLoader->register();
?>