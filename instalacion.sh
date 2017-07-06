#!/bin/bash  

   
echo -e "Comenzando instalaciones...\n\n"

#Install Apache
echo -e "Instalando APACHE\n"
sudo apt-get install apache2


#Install PHP7
echo -e "\n\nInstalando PHP7\n"
sudo apt-get install php7.0 libapache2-mod-php7.0


#Installing MongoDB
echo -e "\n\nInstalando MongoDB\n"
sudo apt-get install mongodb
sudo apt-get install php-mongodb


#Installing MongoDB Driver for PHP
echo -e "\n\nInstalando MongoDB Driver\n"
sudo apt-get install php-pear
sudo pecl install mongodb
sudo apt-get install php-dev


echo -e "\n\nPrimeras instalaciones realizadas\nA continuacion añada la linea 'extension=mongodb.so' en el fichero php.ini"

#service restart apache2


exit
