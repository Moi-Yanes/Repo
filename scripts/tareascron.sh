#!/bin/sh

THE_CLASSPATH=
CP=
PROGRAM_NAME=src/main.java

cd /var/www/html/TFG/java/


echo "************************* COMPILANDO CODIGO JAVA *************************\n"
for i in `ls ./lib/*.jar` 
do
	echo ${i}
	THE_CLASSPATH=${THE_CLASSPATH}:${i}
done

javac -classpath ".:${THE_CLASSPATH}" -d clases src/es/rss/*.java src/es/mongodb/MongoClienteNoticia.java $PROGRAM_NAME


if [ $? -eq 0 ] 
then

	echo "\nCOMPILACION COMPLETADA!\n"

	cd clases/
	for i in `ls ./../lib/*.jar` 
	do
		echo ${i}
		CP=${CP}:${i}
	done

	echo "\n************************* EJECUTANDO CODIGO JAVA *************************\n"
	java -cp "${CP}:." Main
fi



cd /var/www/html/TFG/php/
echo "\n************************* EJECUTANDO CODIGO PHP *************************"
	php main.php

echo "\n************************* TAREAS TERMINADAS! *************************\n"
