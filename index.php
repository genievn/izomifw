<?php
ini_set('memory_limit','64M');

$ds = DIRECTORY_SEPARATOR;			//Directory separator
$abs = dirname(__FILE__).$ds;		//Absolute path of the root of website
//Loading the base class
require($abs."{$ds}core{$ds}base{$ds}Lambda.php");
require($abs."{$ds}core{$ds}base{$ds}Object.php");
require($abs."{$ds}core{$ds}base{$ds}Aop.php");
require($abs."{$ds}core{$ds}base{$ds}Config.php");
require($abs."{$ds}core{$ds}base{$ds}Import.php");
require($abs."{$ds}core{$ds}base{$ds}Loader.php");
Import::getInstance()->setImportRoot($abs);
import('core.base.common.*');
#import('core.base.database.*');
import('core.base.database.Doctrine2');
import('core.base.tool.*');
//import('core.base.form.*');
//import('core.base.form.extjs.*');
import('core.base.fs.*');
import('core.lib.*');
import('core.lib.yaml.*');
import('core.lib.workflow.*');
import('core.*');
import('utils.*');

//Loading config
Config::getInstance()->loadConfig($abs.'config');
config('.root.abs',$abs);
$site = object('izSite');
$site->toString();
?>