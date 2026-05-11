# Publicacion en Facebook desde ETC

## Objetivo

La funcionalidad permite que una publicacion creada en la aplicacion se publique en una Pagina de Facebook.

El flujo completo es:

```txt
React genera el PNG final
        ↓
React envia ese PNG al backend
        ↓
Laravel guarda el PNG en storage
        ↓
Laravel publica la imagen en Facebook
        ↓
Laravel guarda el resultado en la BD
```

La clave de seguridad es que el token de Facebook nunca esta en React. El token vive en el `.env` del backend, porque React se ejecuta en el navegador y cualquier persona podria inspeccionar su codigo.

## Responsabilidades

### Frontend

El frontend se encarga de:

- mostrar la publicacion al usuario;
- permitir editar titulo, contenido, hashtags y estado;
- generar visualmente el PNG final;
- descargar el PNG;
- enviar el PNG al backend cuando se pulsa "Publicar en Facebook".

### Backend

El backend se encarga de:

- validar la peticion;
- comprobar permisos del usuario;
- guardar el PNG en `storage`;
- usar el token privado de Facebook;
- publicar en la Pagina de Facebook;
- guardar el resultado en base de datos.

## Archivos principales del backend

```txt
routes/api.php
app/Http/Controllers/FacebookPublicacionController.php
app/Http/Requests/PublishFacebookRequest.php
app/Services/FacebookPageService.php
config/services.php
app/Models/Publicacion.php
database/migrations/...add_facebook_publication_fields...
```

Tambien se usan estos archivos para resolver el problema CORS del canvas:

```txt
app/Http/Controllers/MediaController.php
app/Http/Controllers/PerfilController.php
```

## Configuracion en config/services.php

Se anadio la configuracion de Facebook:

```php
'facebook' => [
    'page_id' => env('FACEBOOK_PAGE_ID'),
    'page_access_token' => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
    'graph_version' => env('FACEBOOK_GRAPH_VERSION', 'v25.0'),
],
```

Esto lee los valores desde el `.env`:

```env
FACEBOOK_PAGE_ID=
FACEBOOK_PAGE_ACCESS_TOKEN=
FACEBOOK_GRAPH_VERSION=v25.0
```

`config/services.php` es el lugar habitual en Laravel para configurar servicios externos como Gemini, Facebook, correo, etc.

## Servicio FacebookPageService

Archivo:

```txt
app/Services/FacebookPageService.php
```

Este servicio encapsula la comunicacion con Facebook.

### ensureConfigured()

```php
public function ensureConfigured(): void
```

Comprueba que existan:

```php
config('services.facebook.page_id')
config('services.facebook.page_access_token')
```

Si falta algun dato, lanza una excepcion. Esto evita intentar publicar si Facebook no esta configurado.

### publishPhoto()

```php
public function publishPhoto(string $imagePath, string $fileName, string $message): array
```

Recibe:

```txt
$imagePath  -> ruta absoluta del PNG guardado
$fileName   -> nombre del archivo
$message    -> texto de la publicacion
```

Construye la URL de Graph API:

```php
$url = "https://graph.facebook.com/{$version}/{$pageId}/photos";
```

Y envia la imagen usando:

```php
Http::attach(...)
```

`Http` viene de:

```php
use Illuminate\Support\Facades\Http;
```

Este facade permite hacer peticiones HTTP desde Laravel.

La peticion a Facebook incluye:

```php
'caption' => $message,
'published' => true,
'access_token' => $accessToken,
```

Si Facebook devuelve error, el servicio lanza una excepcion con el mensaje de Meta.

## Controlador FacebookPublicacionController

Archivo:

```txt
app/Http/Controllers/FacebookPublicacionController.php
```

Metodo principal:

```php
public function publish(
    PublishFacebookRequest $request,
    Publicacion $publicacion,
    FacebookPageService $facebook
)
```

Laravel inyecta automaticamente:

```txt
$request      -> peticion validada
$publicacion  -> publicacion obtenida por route model binding
$facebook     -> servicio de Facebook
```

### Pasos del metodo

1. Carga relaciones:

```php
$publicacion->loadMissing('pieza.medias', 'reds');
```

2. Autoriza:

```php
$this->authorize('update', $publicacion);
```

Solo el propietario de la pieza o un administrador puede publicar.

3. Construye el mensaje:

```php
$message = $request->input('mensaje') ?: $this->buildMessage($publicacion);
```

Si el frontend manda mensaje, se usa. Si no, se crea con titulo, contenido y hashtags.

4. Comprueba configuracion:

```php
$facebook->ensureConfigured();
```

5. Recibe la imagen:

```php
$image = $request->file('imagen');
```

6. Crea un nombre de archivo:

```php
$fileName = $this->buildFileName($publicacion, $image->extension());
```

Aqui se usa:

```php
use Illuminate\Support\Str;
```

`Str::slug()` convierte un texto en un nombre valido para URL o archivo:

```txt
"Muneco de Nieve" -> "muneco-de-nieve"
```

7. Guarda el PNG:

```php
$imagePath = $image->storeAs('publicaciones/facebook', $fileName, 'public');
```

Esto guarda el archivo en:

```txt
storage/app/public/publicaciones/facebook
```

8. Obtiene la ruta absoluta:

```php
$absoluteImagePath = Storage::disk('public')->path($imagePath);
```

`Storage` viene de:

```php
use Illuminate\Support\Facades\Storage;
```

Sirve para trabajar con archivos en Laravel.

9. Publica en Facebook:

```php
$facebookResponse = $facebook->publishPhoto($absoluteImagePath, $fileName, $message);
```

10. Guarda resultado en la tabla pivote `publicacion_red`:

```php
$publicacion->reds()->syncWithoutDetaching([
    $facebookRed->id => [
        'fecha_vencimiento' => now()->addMonths(3)->toDateString(),
        'estado_publicacion' => 'publicado',
        'imagen_publicada_path' => $imagePath,
        'external_photo_id' => $facebookResponse['id'] ?? null,
        'external_post_id' => $facebookResponse['post_id'] ?? null,
        'published_at' => now(),
        'error' => null,
    ],
]);
```

`syncWithoutDetaching()` asocia Facebook sin eliminar otras redes que pudiera tener la publicacion.

11. Marca la publicacion como publicada:

```php
$publicacion->update(['estado' => 'publicado']);
```

12. Devuelve respuesta JSON al frontend.

Si Facebook falla, se guarda el intento con:

```txt
estado_publicacion = error
error = mensaje devuelto por Facebook
```

## Request PublishFacebookRequest

Archivo:

```txt
app/Http/Requests/PublishFacebookRequest.php
```

Valida:

```php
'imagen' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192',
'mensaje' => 'nullable|string|max:5000',
```

Esto significa:

- debe venir una imagen;
- debe ser un archivo real;
- solo acepta jpg, jpeg, png o webp;
- maximo 8 MB;
- el mensaje es opcional y puede tener hasta 5000 caracteres.

## Base de datos

Se amplio la tabla pivote `publicacion_red` con campos nuevos:

```txt
estado_publicacion
imagen_publicada_path
external_photo_id
external_post_id
published_at
error
```

Esta informacion vive en la tabla pivote porque una publicacion puede publicarse en varias redes:

```txt
Publicacion 11 -> Facebook
Publicacion 11 -> Instagram
Publicacion 11 -> LinkedIn
```

Cada red puede tener su propio estado, fecha, imagen publicada, error e ID externo.

## Modelo Publicacion

En `Publicacion.php` se amplio la relacion:

```php
public function reds(): BelongsToMany
{
    return $this->belongsToMany(Red::class, 'publicacion_red')
        ->withPivot([
            'fecha_vencimiento',
            'estado_publicacion',
            'imagen_publicada_path',
            'external_photo_id',
            'external_post_id',
            'published_at',
            'error',
        ])
        ->withTimestamps();
}
```

`withPivot()` indica a Laravel que, al cargar las redes de una publicacion, tambien debe traer esos campos extra de la tabla pivote.

## Rutas

En `routes/api.php`:

```php
Route::post('/publicacion/{publicacion}/facebook', [FacebookPublicacionController::class, 'publish']);
```

Esta ruta esta dentro del grupo protegido por:

```txt
auth:sanctum
verified
```

Solo usuarios autenticados y verificados pueden publicar.

## Problema CORS del canvas

El PNG se genera con canvas en el frontend.

El problema era que las imagenes venian de:

```txt
/storage/imagenes/...
/storage/logos/...
```

El navegador podia mostrarlas en pantalla, pero no podia leerlas dentro del canvas por CORS.

Para resolverlo se crearon endpoints API autenticados:

```txt
GET /api/media/{media}/archivo
GET /api/perfil/logo/archivo
```

### MediaController::archivo()

Este metodo:

- carga la pieza;
- comprueba si el usuario es administrador o propietario;
- verifica que el archivo exista;
- devuelve la imagen desde storage.

Usa:

```php
Storage::disk('public')->response($media->path);
```

### PerfilController::logoArchivo()

Hace lo mismo, pero con el logo del perfil autenticado.

## Archivos principales del frontend

```txt
src/components/PublicacionPreview.jsx
src/utils/exportPublicacionImage.js
src/hooks/usePiezaDetalle.js
src/pages/PiezaDetallePage.jsx
```

## PublicacionPreview.jsx

Este componente:

- muestra los campos editables de la publicacion;
- muestra la previsualizacion del PNG;
- permite descargar el PNG;
- permite publicar en Facebook.

### crearBlobUrlAutenticada()

Hace una peticion a una ruta API usando el token del usuario:

```js
fetch(`${apiUrl}/${endpoint}`, {
  headers: {
    Accept: "image/*",
    Authorization: `Bearer ${token}`,
  },
});
```

Luego convierte la respuesta en `Blob`:

```js
const blob = await respuesta.blob();
return URL.createObjectURL(blob);
```

Esto crea una URL temporal local:

```txt
blob:http://localhost:5174/...
```

Esa URL si puede usarse en canvas sin problemas de CORS.

### crearImagenBlobUrl()

Obtiene la imagen de la pieza desde:

```txt
media/{media.id}/archivo
```

### crearLogoBlobUrl()

Obtiene el logo desde:

```txt
perfil/logo/archivo
```

### descargarImagen()

Genera el PNG y lo descarga.

Pasos:

1. Obtiene imagen como blob local.
2. Obtiene logo como blob local.
3. Crea un `perfilCanvas` con el logo temporal.
4. Llama a `exportarPublicacionPng()`.
5. Libera las URLs temporales con `URL.revokeObjectURL()`.

### publicarFacebook()

Genera el PNG y lo envia al backend.

Pasos:

1. Comprueba que la publicacion tenga `id`.
2. Obtiene imagen y logo como blobs locales.
3. Genera el PNG con `crearPublicacionPngBlob()`.
4. Crea un `FormData`.
5. Anade el archivo:

```js
formData.append("imagen", blob, `publicacion-${publicacion.id}.png`);
```

6. Anade el mensaje:

```js
formData.append("mensaje", ...)
```

7. Llama a:

```js
onPublicarFacebook(formData)
```

## exportPublicacionImage.js

Este archivo genera el canvas.

Funciones principales:

### cargarImagen(src)

Carga una imagen para poder pintarla en canvas.

### dibujarImagenContain()

Dibuja la imagen completa sin cortarla.

### dibujarImagenCover()

Dibuja una imagen cubriendo todo el espacio, aunque recorte.

Se usa para el fondo ampliado y desenfocado.

### crearCanvasPublicacion()

Construye la imagen final:

1. Fondo oscuro.
2. Imagen de fondo ampliada con blur.
3. Capa oscura semitransparente.
4. Imagen principal completa.
5. Degradado inferior.
6. Titulo.
7. Contenido.
8. Hashtags.
9. Logo.
10. Nombre, telefono y web.

### crearPublicacionPngBlob()

Devuelve el PNG como `Blob`.

Se usa para publicar en Facebook.

### exportarPublicacionPng()

Descarga el PNG en el ordenador.

Se usa para el boton "Descargar imagen PNG".

## usePiezaDetalle.js

Se anadio:

```js
publicarEnFacebook(formData)
```

Este metodo llama al backend:

```js
postForm(`publicacion/${publicacion.id}/facebook`, formData)
```

Si va bien, actualiza la publicacion y muestra mensaje de exito.

Si falla, muestra el mensaje devuelto por el backend.

## PiezaDetallePage.jsx

Conecta el hook con el componente:

```jsx
<PublicacionPreview
  publicandoFacebook={publicandoFacebook}
  onPublicarFacebook={publicarEnFacebook}
/>
```

No contiene la logica de publicacion. Solo pasa props.

## Relacion con Gemini

La logica de IA y la logica de Facebook estan separadas.

Gemini se usa para generar:

```txt
titulo
contenido
hashtags
```

Facebook se usa para publicar:

```txt
imagen PNG
mensaje
```

Ambas cosas viven en backend porque usan claves privadas:

```txt
GEMINI_API_KEY
FACEBOOK_PAGE_ACCESS_TOKEN
```

El frontend nunca debe tener esas claves.

## Explicacion breve para clase

La publicacion en Facebook se implementa con una arquitectura separada. React genera el PNG final en el navegador y lo envia como archivo al backend. Laravel valida la peticion, comprueba permisos, guarda la imagen en storage y usa un servicio especifico para comunicarse con la Graph API de Facebook. El token de Facebook esta en el `.env` del backend, nunca en el frontend. Ademas, el resultado de la publicacion se guarda en la tabla pivote `publicacion_red`, porque una publicacion puede estar asociada a varias redes sociales.

## Seguridad

- `.env` no se sube a Git.
- El token de Facebook vive solo en backend.
- El frontend llama a Laravel, no directamente a Facebook.
- Las imagenes usadas en canvas se obtienen por endpoints API autenticados.
- Si el token se expone accidentalmente, debe regenerarse en Meta.
