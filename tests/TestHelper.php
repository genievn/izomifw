<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH);

// Include path
set_include_path(
    '.'
    . PATH_SEPARATOR . BASE_PATH . '/core/base'
    . PATH_SEPARATOR . get_include_path()
);

$ds = DIRECTORY_SEPARATOR;			//Directory separator
$abs = BASE_PATH . $ds;				//Absolute path of the root of website

//Loading the base class
require($abs."{$ds}core{$ds}base{$ds}Lambda.php");
require($abs."{$ds}core{$ds}base{$ds}Object.php");
require($abs."{$ds}core{$ds}base{$ds}Aop.php");
require($abs."{$ds}core{$ds}base{$ds}Config.php");
require($abs."{$ds}core{$ds}base{$ds}Import.php");


Import::getInstance()->setImportRoot($abs);
import('core.base.common.*');
import('core.base.database.*');
import('core.base.tool.*');
import('core.base.form.*');
import('core.base.form.extjs.*');
import('core.base.fs.*');
import('core.lib.yaml.*');
import('core.lib.workflow.*');

import('core.lib.*');
import('core.*');
import('utils.*');

// Define application environment
define('APPLICATION_ENV', 'testing');
?>