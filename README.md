## Proyecto facturación de agua potable

## Crear Modelo

php artisan make:model Persona

## Lista de rutas disponibles

php artisan route:list

## Comando migraciones

\*\*Elimina toda la info de las tablas de la BD
php artisan migrate:reset

php artisan migrate

\*\*Elimina la base de datos
php artisan migrate:rollback

\*\*para mantener la data de los pacientes
php artisan migrate --seed

\*\*Elimina las tablas y luego las crea otra vez con datos de relleno
php artisan migrate:fresh --seed

## Codigos de respuestas http

## Crear una Migración

php artisan make:model Etiqueta -m
php artisan make:migration create_role_usuario_table --create=

## Creación de Middlewares

php artisan make:middleware ApiAuthMiddleware

## Autorizaciones

php artisan make:policy PersonaPolicy --model=Persona

## Crear controladores

php artisan make:controller UserController --resource

## Para validacion de datos antes de guardar en la base de datos

php artisan make:request RegisterRequest

## Para que es el token?

Si ell usuario tiene asignado un token puede acceder a las rutas de la API.
Si el usuario no tiene Token no va a poder acceder.

## Factorys rellenar datos con Factoryes

php artisan make:factory PersonaFactory
