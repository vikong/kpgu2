#!/bin/bash
# Настройка соединения с базой данных
RED='\033[0;31m'
GREEN='\033[0;32m'
BROWN='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${BROWN}Настройка соединения с СУБД${NC}"
echo "Введите хост (localhost)"
read host
if [ -z $host ]
then
host="localhost"
fi

echo "Введите порт (5432)"
read port
if [ -z $port ]
then
port=5432
fi

echo "Введите имя базы данных"
read database

echo "Введите имя пользователя"
read username

echo "Введите пароль"
read password

export DB_HOST=$host
export DB_PORT=$port
export DB_DATABASE=$database
export DB_USERNAME=$username
export DB_PASSWORD=$password

echo 'Проверка подключения'
if php ./kpgu2/artisan database:test
then
echo -e "${GREEN}Успешно подключено${NC}"
else
echo -e "${RED}Не удалось подключиться${NC}"
exit 1
fi

echo 'Инициация БД'
php ./kpgu2/artisan migrate

