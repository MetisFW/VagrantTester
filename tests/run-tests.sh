#!/bin/sh

# Path to this script's directory
dir=$(cd `dirname $0` && pwd)

phpBin=php

# Path to test runner script
runnerScript="$dir/../libs/nette/tester/Tester/tester.php"
if [ ! -f "$runnerScript" ]; then
	echo "Nette Tester is missing. You can install it using Composer:" >&2
	echo "php composer.phar update --dev." >&2
	exit 2
fi

if [ $# -ge 1 ]; then
  runTests=$1
  shift
else
  runTests="$dir"
fi

# cleanup
rm -rf $dir/temp/test*
find $runTests -name output -exec rm -r {} \;

# run tests
$phpBin "$runnerScript" -j 20 -s -c "$dir/php.ini" -p "$phpBin" "$runTests" $@
error=$?

# Print *.actual content if tests failed
if [ $error -ne 0 ]; then
  echo "" >&2;
  for i in $(find "$runTests" -name \*.actual); do
    echo "# --- $i";
    sed -e 's/^/# /' $i;
    echo; echo "#";
  done
  exit $error
fi

exit $error
