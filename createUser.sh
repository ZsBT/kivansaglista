#!/bin/bash
#
#	felhasználó létrehozása, a docker környezetben kell futtatni.
#

cd -P $(dirname $0)
#docker-compose -f docker/docker-compose.yml exec szerviz app/kivansag/szerviz/phpDocumentor.sh

docker-compose -f docker/docker-compose.yml exec kivansag-szerviz app/kivansag/szerviz/createuser.php $*

exit 0
