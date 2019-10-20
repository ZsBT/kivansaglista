#!/bin/bash
#
#	ha nincs docker es docker-compose telepitve, Debian Linux alatt igy telepitem
#
_install_docker_compose(){

   sudo apt -y install apt-transport-https ca-certificates curl gnupg2 software-properties-common

   curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -

   sudo add-apt-repository -y \
      "deb [arch=amd64] https://download.docker.com/linux/debian \
      $(lsb_release -cs) \
      stable"

   sudo apt update
   sudo apt -y install docker-ce docker-ce-cli docker-compose containerd.io

   sudo usermod -a -G docker $USER
}


#
###
#####
###
#
cd -P $(dirname $0) || exit 1
which docker-compose || _install_docker_compose || exit 2
docker-compose up --build --no-start || exit 3

exit 0
