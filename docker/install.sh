#
#	docker kontenerek epitese 
#


docker-compose up --build


exit
#
#	ha nincs docker es docker-compose telepitve, peldaul Debian Linux alatt igy telepitem
#

sudo apt -y install apt-transport-https ca-certificates curl gnupg2 software-properties-common

curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -

sudo add-apt-repository -y \
   "deb [arch=amd64] https://download.docker.com/linux/debian \
   $(lsb_release -cs) \
   stable"

sudo apt update
sudo apt -y install docker-ce docker-ce-cli docker-compose containerd.io

sudo usermod -a -G docker $USER
