# ETC - Backend

API REST desarrollada con Laravel 12 para la gestión de usuarios, piezas, imágenes, publicaciones, generación de contenido textual con IA (Gemini) y publicación en redes sociales.

El frontend está en un repositorio independiente:

```text
https://github.com/MJGrauVi/etc_frontend
```

## Puesta En Marcha Local

1. Clonar el repositorio:

```bash
git clone https://github.com/MJGrauVi/etc.git
cd etc
```

2. Crear el archivo de entorno:

```bash
cp .env.example .env
```

3. Ajustar las URLs locales en `.env`:

```env
APP_URL=http://localhost:8095
FRONTEND_URL=http://localhost:5173
```

4. Configurar la base de datos para Sail/PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=etc
DB_USERNAME=sail
DB_PASSWORD=password
```

5. Instalar dependencias PHP:

```bash
composer install
```

Este comando crea la carpeta `vendor`, necesaria para poder usar Laravel y Sail. Para instalar el proyecto en otro equipo se recomienda `composer install` y no `composer update`, porque respeta las versiones bloqueadas en `composer.lock`.

6. Levantar los contenedores:

```bash
./vendor/bin/sail up -d
```

7. Generar la clave de Laravel:

```bash
./vendor/bin/sail artisan key:generate
```

8. Crear tablas y datos de prueba:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

9. Crear el enlace publico de storage:

```bash
./vendor/bin/sail artisan storage:link
```

10. Limpiar cache de configuracion:

```bash
./vendor/bin/sail artisan optimize:clear
```

## URLs Locales

```text
API:     http://localhost:8095/api
Storage: http://localhost:8095/storage
Mailpit: http://localhost:8025
```

## Variables Sensibles

Estas variables deben configurarse en `.env`, pero no deben subirse a GitHub:

```env
GEMINI_API_KEY=
FACEBOOK_PAGE_ID=
FACEBOOK_PAGE_ACCESS_TOKEN=
FACEBOOK_GRAPH_VERSION=v25.0
```

`GEMINI_API_KEY` permite generar contenido textual con IA a partir de imagenes.

`FACEBOOK_PAGE_ID` y `FACEBOOK_PAGE_ACCESS_TOKEN` permiten publicar en una pagina de Facebook configurada para la demo.

## Credenciales De Prueba

Despues de ejecutar los seeders, se crean usuarios de demostracion:

```text
Administrador: admin@admin.com
Administrador ETC: etc-apps@proton.me
Usuario: usuario@usuario.com
Titufas: titufas@gmail.com
Invitado: invitado@invitado.com
Contrasena: ******
```

## Email Y Verificacion

En desarrollo se usa Mailpit para revisar correos enviados por la aplicacion.

```text
http://localhost:8025
```

Si se trabaja en Codespaces, el puerto de Mailpit debe hacerse publico para poder abrir los enlaces de verificacion desde el navegador.

## Despliegue/Demo En GitHub Codespaces

Codespaces se usa como entorno remoto de demostracion, no como despliegue de produccion definitivo.

1. Abrir el repositorio backend en Codespaces.
2. Esperar a que arranque el contenedor.
3. Hacer publico el puerto del backend, normalmente `8095`.
4. Copiar la URL publica del puerto `8095`.
5. Ajustar `.env`:

```env
APP_URL=https://tu-codespace-8095.app.github.dev
FRONTEND_URL=https://tu-codespace-5173.app.github.dev
```

6. Limpiar cache:

```bash
php artisan optimize:clear
```

Si el comando se ejecuta desde fuera del contenedor:

```bash
docker exec etc-laravel.test-1 php artisan optimize:clear
```

7. Ejecutar migraciones y seeders si se quiere regenerar la base de datos:

```bash
php artisan migrate:fresh --seed
```

8. Crear enlace de storage si no existe:

```bash
php artisan storage:link
```

## Comandos Utiles

Ver rutas:

```bash
./vendor/bin/sail artisan route:list
```

Limpiar cache:

```bash
./vendor/bin/sail artisan optimize:clear
```

Entrar a Tinker:

```bash
./vendor/bin/sail artisan tinker
```

Apagar contenedores:

```bash
./vendor/bin/sail down
```

Reiniciar todo eliminando volumenes:

```bash
./vendor/bin/sail down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan storage:link
```

## Notas

- Las imagenes de ejemplo versionadas estan en `database/seeders/images`.
- Los seeders copian esas imagenes a `storage/app/public`.
- La URL publica de cada imagen se genera con `APP_URL`.
- El frontend consume la API mediante `VITE_API_URL`.
