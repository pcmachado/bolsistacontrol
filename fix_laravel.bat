@echo off

echo Criando pastas necessarias do Laravel...

mkdir bootstrap\cache 2>nul

mkdir storage\app 2>nul
mkdir storage\framework 2>nul
mkdir storage\framework\cache 2>nul
mkdir storage\framework\sessions 2>nul
mkdir storage\framework\views 2>nul
mkdir storage\logs 2>nul

echo Limpando caches...

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo Recriando cache do sistema...

php artisan optimize

echo.
echo Processo concluido!
pause