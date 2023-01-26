#!/bin/bash
# Установка модулей КПГУ2
RED='\033[0;31m'
GREEN='\033[0;32m'
BROWN='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Проверка системы${NC}"

if ! php -v
then
echo -e "${RED}Отсутствует фрейморк PHP${NC}"
exit 1
fi

pgsql=`dpkg -s php7.0-pgsql | grep "Status" `
if [ -z "$pgsql" ] 
then
echo 'Установка драйвера pgsql'
sudo apt-get install php7.0-pgsql
else
echo 'Драйвер pgsql - ok'
fi 

echo -e "${GREEN}Разархивирование модулей${NC}"

unzip kpgu2.zip -d ./kpgu2
source ./kpgu2setup.sh
echo "Установка завершена"

