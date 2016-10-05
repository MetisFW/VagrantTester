<?php

use MetisFW\VagrantTester\VagrantMachine;
use Tester\Assert;

require __DIR__."/bootstrap.php";

Assert::exception(
  function() {
    $m = VagrantMachine::id("not-exists");
  },
  'MetisFW\VagrantTester\VagrantMachineException'
);

$m = VagrantMachine::id("vagrant-tester");

Assert::contains('sbin', $m->run("ls /"));

Assert::exception(
  function() use($m) {
    $m->run("wrong");
  },
  'MetisFW\VagrantTester\VagrantMachineException'
);

Assert::true($m->fileExists("/bin/bash"));
Assert::false($m->fileExists("/not-exists"));

Assert::equal("content of test.txt", $m->file("/synced/test.txt"));

Assert::exception(
  function() use($m) {
    $m->file('/not-exists');
  },
  'MetisFW\VagrantTester\VagrantMachineException'
);

VagrantMachine::$output = function($machine, $text) {
  file_put_contents(TEMP_DIR . "/$machine.output", $text, FILE_APPEND);
};

$m->run("ls /");

Assert::contains('>>[vagrant-tester]>> ls /', file_get_contents(TEMP_DIR . "/vagrant-tester.output"));

