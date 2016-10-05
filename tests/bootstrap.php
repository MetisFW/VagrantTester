<?php

use MetisFW\VagrantTester\VagrantMachine;
use Tester\Assert;

require __DIR__."/../libs/autoload.php";
require __DIR__."/../src/MetisFW/VagrantTester/VagrantMachine.php";
require __DIR__."/../src/MetisFW/VagrantTester/VagrantMachineException.php";

/**
 * @param object $val Function added by nette tester.
 * @return object only return the parameter.
 * @SuppressWarnings(ShortMethodName)
 */
function id($val) {
  return $val;
}

// create temporary directory
define('TEMP_DIR', __DIR__.'/temp/test-'.basename($argv[0]).'-'.getmypid());
@mkdir(dirname(TEMP_DIR));
Tester\Helpers::purge(TEMP_DIR);

define('LOCK_DIR', __DIR__.'/temp/locks');
@mkdir(LOCK_DIR);

// ensure Tester is avaliable
if(!class_exists('Tester\Assert')) {
  echo "Install Nette Tester using `composer update`\n";
  exit(1);
}

// setup environment
VagrantMachine::$locksDir = LOCK_DIR;
VagrantMachine::$tempDir = TEMP_DIR;
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');
