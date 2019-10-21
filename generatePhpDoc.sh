#!/bin/bash
#
#	PHPdoc legyártása
#

cd -P $(dirname $0)
docker-compose -f docker/docker-compose.yml exec kivansag-szerviz app/kivansag/szerviz/phpDocumentor.sh

exit 0
