# MetisFW/PayPal

## Setup

This must be done beafore connectiong to any Vagrant machine

```php
VagrantMachine::$locksDir = '/path/to/locks';
VagrantMachine::$tempDir = '/path/to/temp';
```

By default Vagrant machine prints log messages using echo. You can use custom output function:

```php
VagrantMachine::$output = function($machine, $text) {
  file_put_contents("$machine.output", $text, FILE_APPEND);
}
```

##Usage

##### Connect to Vagrant VM

```php
$machine = VagrantMachine::id("vagrant-tester");
```

If you connect to multiple machines connection must be done in alphabetical order to prevent deadlocks.

##### Execute command inside Vagrant VM

```php
$output = $machine->run("ls /")
```

##### Get content of file from Vagrant VM

```php
$fileContent = $machine->file("/etc/hosts")
```

##### Chech if file exists inside Vagrant VM

```php
$fileexists = $machine->fileExists("/etc/hosts")
```
