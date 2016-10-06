<?php

namespace MetisFW\VagrantTester;

use Tester\Environment;

class VagrantMachine {

  private $machine;

  private $ssh;

  private static $locked = array();

  public static $locksDir;

  public static $tempDir;

  public static $output;

  public function __construct($machine) {
    $this->machine = $machine;
    if(self::$locksDir == null) {
      throw new VagrantMachineException('Locks dir not set. Use VagrantMachine::$locksDir = "/path" to set it.');
    }
    if(self::$tempDir == null) {
      throw new VagrantMachineException('Temp dir not set. Use VagrantMachine::$tempDir = "/path" to set it.');
    }
    $this->checkLockOrder($machine);
    Environment::lock($machine, self::$locksDir);
    $this->ssh = $this->getSshConfigFilePath();
  }

  private function checkLockOrder($machine) {
    foreach(self::$locked as $id => $value) {
      if($id > $machine) {
        throw new VagrantMachineException(
          "Machines must be required in alphabetical order to prevent deadlocks." .
          "Machine $machine must by required before $id"
        );
      }
    }
    self::$locked[$machine] = true;
  }

  public static function id($machine) {
    return new VagrantMachine($machine);
  }

  protected function out($text) {
    $output = self::$output;
    $text = "$text\n";
    if($output === null) {
      echo $text;
    } else {
      $output($this->machine, $text);
    }
  }

  public function getSshConfig()
  {
    $output = array();
    $status = null;
    $command = "vagrant ssh-config {$this->machine}";
    exec($command, $output, $status);
    $outputStr = implode("\n", $output);
    if ($status !== 0) {
      throw new VagrantMachineException("Can't connect to '{$this->machine}': Get exit status $status for '$command'");
    }
    return $outputStr;
  }
  
  private function getSshConfigFilePath() {
    $filePath = self::$tempDir."/{$this->machine}.ssh";
    if(!file_exists($filePath)) {
      file_put_contents($filePath, $this->getSshConfig());
      $this->out("Load ssh config $filePath");
    } else {
      $this->out("Use cached ssh config $filePath");
    }
    return $filePath;
  }

  public function run($command, $expectedStatus = 0)
  {
    list($status, $outputStr) = $this->executeCommand($command);
    if ($status != $expectedStatus) {
      throw new VagrantMachineException("Unexpected exit status ($status should be $expectedStatus) for '$command'");
    }
    return $outputStr;
  }

  public function file($path) {
    return $this->run("cat " . escapeshellarg($path));
  }

  public function fileExists($path) {
    list($status, $outputStr) = $this->executeCommand("test -f " . escapeshellarg($path));
    return ($status == 0);
  }

  protected function executeCommand($command) {
    $targetCommand = "sudo -sE " . $command;
    $fullCommand = "cat {$this->ssh} | ssh -q -t -t -F/dev/stdin -M {$this->machine} " . escapeshellarg($targetCommand) . " 2>&1";
    $this->out(">>[{$this->machine}]>> $command ($fullCommand)");
    $output = array();
    $status = null;
    $startTime = time();
    exec($fullCommand, $output, $status);
    $endTime = time();
    $outputStr = implode("\n", $output);
    $timeElapsed = $endTime - $startTime;
    $this->out("<<[{$this->machine}]({$timeElapsed}s)<< [$status] $outputStr");
    return array($status, $outputStr);
  }

}
