#!/bin/bash
TESTS_DIR=$(dirname $0)/../tests/

cd $TESTS_DIR
/opt/lampp/bin/phpunit --configuration phpunit.xml --verbose
echo ""
echo ""
