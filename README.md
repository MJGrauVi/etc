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

9. Crear el enlace público de storage:

```bash
./vendor/bin/sail artisan storage:link
```
10. Revisar permisos de storage:

```bash
./vendor/bin/sail shell
chown -R sail:root storage/app/public
chmod -R 775 storage/app/public
```

11. Limpiar cache de configuración:

```bash
./vendor/bin/sail artisan optimize:clear
```

## URLs Locales

```text
API:     http://localhost:8095/api
Storage: http://localhost:8095/storage/imagenes/gitanilla1.jpeg
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

`GEMINI_API_KEY` permite generar contenido textual con IA a partir de imágenes.

`FACEBOOK_PAGE_ID` y `FACEBOOK_PAGE_ACCESS_TOKEN` permiten publicar en una página de Facebook configurada para la demo.

## Credenciales De Prueba

Después de ejecutar los seeders, se crean usuarios de demostración:

```text
Administrador: admin@admin.com
Administrador ETC: etc-apps@proton.me
Usuario: usuario@usuario.com
Usuario: titufas@gmail.com
Contrasena: ******
```

## Email Y Verificacion

En desarrollo se usa Mailpit para revisar correos enviados por la aplicación.

```text
http://localhost:8025
```

Si se trabaja en Codespaces, el puerto de Mailpit debe hacerse público para poder abrir los enlaces de verificación desde el navegador.

## Despliegue/Demo En GitHub Codespaces

Codespaces se usa como entorno remoto de demostración, no como despliegue de produccion definitivo.

1. Abrir el repositorio backend en Codespaces.
2. Esperar a que arranque el contenedor.
3. Hacer público el puerto del backend, normalmente `8095`.
4. Copiar la URL pública del puerto `8095`.
5. Ajustar `.env`:

```env
APP_URL=https://tu-codespace-8095.app.github.dev
FRONTEND_URL=https://tu-codespace-5173.app.github.dev
```

6. Limpiar caché:

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
9. Ajustar permisos de storage:

```bash
chown -R sail:root storage/app/public
chmod -R 775 storage/app/public
```

## Comandos Utiles

Ver rutas:

```bash
./vendor/bin/sail artisan route:list
```

Limpiar caché:

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

Reiniciar todo eliminando volúmenes:

```bash
./vendor/bin/sail down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan storage:link
```

##m
- Las imágenes de ejemplo versionadas están en `database/seeders/images`.
- Los seeders copian esas imágenes a `storage/app/public`.
- La URL pública de cada imagen se genera con `APP_URL`.
- El frontend consume la API mediante `VITE_API_URL`.
