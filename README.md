# Proyecto Laravel con Sail y PostgreSQL

Este proyecto utiliza **Laravel Sail** como entorno de desarrollo basado en Docker y **PostgreSQL** como base de datos.  
Este README explica cómo ponerlo en marcha en cualquier ordenador (casa, clase, portátil) sin errores.

---

# Requisitos

- Docker Desktop instalado y funcionando
- Git instalado
- Composer (solo si NO usas Sail, pero no es necesario)

---

# Puesta en marcha del proyecto

## 1. Clonar el repositorio

```bash
git clone https://github.com/<TU-USUARIO>/<TU-REPO>.git
cd <TU-REPO>

```

## Crear el archivo .env
``` 
cp .env.example .env

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=etc
DB_USERNAME=******
DB_PASSWORD=******
```
## Levantar el entorno Docker (Sail)
```
./vendor/bin/sail up -d
```

## Instalar dependencias Laravel
```
./vendor/bin/sail composer install
```

## Generar la clave de la aplicacion
``` 
./vendor/bin/sail artisan key:generate
```

# Reset competo del entrono si algo no funciona.
```
./vendor/bin/sail down -v 
./vendor/bin/sail up -d
```

## Acceso a la aplicacion.
```
http://localhost
```
# Despliegue en Github Codespaces.

# Opción2 para despliegue (gratuito en Railway)

Este proyecto puede desplegarse fácilmente en **Railway**, una plataforma gratuita ideal para estudiantes.  
Railway permite ejecutar aplicaciones Laravel online sin configurar servidores manualmente.

---

## 1. Crear cuenta en Railway

Acceder a: https://railway.app  
Puedes iniciar sesión con GitHub en un clic.

---

## 2. Crear un nuevo proyecto desde GitHub

1. En Railway, pulsar **New Project**
2. Seleccionar **Deploy from GitHub Repo**
3. Elegir este repositorio

Railway clonará el proyecto automáticamente.

---

## 3. Añadir PostgreSQL

1. Dentro del proyecto Railway, pulsar **Add Plugin**
2. Seleccionar **PostgreSQL**

Railway generará automáticamente:

- PGHOST
- PGPORT
- PGUSER
- PGPASSWORD
- PGDATABASE
- DATABASE_URL

Estos valores se usarán en las variables de entorno.

---

## 4. Configurar variables de entorno

Ir a **Variables** en Railway y añadir:

APP_ENV=production
APP_DEBUG=false
APP_KEY=<tu clave>

Configurar la base de datos con los valores del plugin PostgreSQL:

DB_CONNECTION=pgsql
DB_HOST=<PGHOST>
DB_PORT=<PGPORT>
DB_DATABASE=<PGDATABASE>
DB_USERNAME=<PGUSER>
DB_PASSWORD=<PGPASSWORD>


---

## 5. Generar APP_KEY

En tu ordenador local:

```bash
php artisan key:generate --show
Copiar el resultado en Railway → APP_KEY.

---
## 6. Configurar el comando de inicio.
 
 
En Railway → Settings → Start Command, escribir:

php artisan serve --host=0.0.0.0 --port=$PORT

7. Ejecutar migraciones en Railway
En Railway → pestaña Shell, ejecutar:

php artisan migrate --force

8. Acceder a la aplicación
Railway generará una URL pública, por ejemplo:

Código
https://tuapp.up.railway.app
