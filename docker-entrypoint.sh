#!/bin/sh

# Esperar a que la base de datos esté disponible
until php -r "\ntry {\n    new PDO(\"mysql:host=database;dbname=club_cms_db\", 'root', 'dwes');\n    exit(0);\n} catch (Exception $e) {\n    exit(1);\n}\n"; do
  echo "Esperando a que la base de datos esté lista..."
  sleep 3
 done

# Ejecutar migraciones y Passport
php artisan migrate --force
php artisan passport:install --force
php artisan vendor:publish --tag=passport-migrations --force
php artisan vendor:publish --tag=passport-config --force

# Ejecutar el comando original (php-fpm)
exec "$@"
