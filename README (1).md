#  Aplicación del Tiempo

La aplicación web esta desarrollada en PHP puro siguiendo el patrón de arquitectura **MVC** . Permite consultar el tiempo atmosférico de cualquier ciudad del mundo usando la API de OpenWeatherMap de forma rapida.

---

##  Funcionalidades

- Búsqueda de ciudades por nombre.
- Tiempo actual.
- Previsión por horas.
- Previsión semanal.
- Gráficas interactivas con Chart.js
- Historial de consultas guardado en base de datos.
- Utilizacion del DAO para el acceso a datos

---

##  Arquitectura MVC

El proyecto sigue el patrón Modelo-Vista-Controlador:

| Capa | Carpeta | Responsabilidad |
|------|---------|-----------------|
| **Model** | `models/` | Acceso a la base de datos |
| **View** | `views/` | HTML que ve el usuario |
| **Controller** | `controllers/` | Une Model y View, llama a la API y prepara los datos |

---

## Estructura de archivos

```
Aplicacion_del_Tiempo_MVC/
  ├── index.php                          ← Es la página principal de la aplicación.                           
  ├── acceso-tiempo-ahora.php            ← Archivo que muestra el tiempo actual de la ciudad buscada.
  ├── acceso-prevision-horas.php         ← Página que muestra la previsión del tiempo por horas
  ├── acceso-prevision-semanal.php       ← Muestra la previsión del tiempo para los próximos días
  ├── acceso-consultas-realizadas.php    ← Página donde se muestra el historial de búsquedas realizadas.
  ├── config.php                         ← API key y configuracion
  ├── Dockerfile                         ← Archivo que define cómo se construye el contenedor de la aplicación.
  ├── docker-compose.yml                 ← Archivo que permite ejecutar toda la aplicación con Docker.
  ├── controllers/
  │     ├── BuscadorController.php        ← Procesa la búsqueda de ciudades y dirige a las diferentes páginas de resultados. 
  │     ├── ActualController.php
  │     ├── HorasController.php
  │     ├── SemanaController.php
  │     └── HistorialController.php
  ├── models/
  │     └── ConsultaModel.php
  ├── views/
  │     ├── buscador.php
  │     ├── tiempo-ahora.php
  │     ├── prevision-horas.php
  │     ├── prevision-semanal.php
  │     └── consultas-realizadas.php
  └── css/
        └── estilo.css
```

---

##  Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y en ejecución
- Cuenta gratuita en [openweathermap.org](https://openweathermap.org/api) para obtener una API Key


---

##  Configuración

### API Key

Abre `config.php` y sustituye el valor por tu clave personal:

```php
define('API_KEY', '174d6d0647e10b03d7fad11527e974d3');
define('API_BASE', 'https://api.openweathermap.org/data/2.5/');
define('UNIDADES', 'metric');
define('IDIOMA',   'es');
```

### Base de datos

Las credenciales se configuran en `docker-compose.yml`:

```yaml
environment:
  DB_HOST: db
  DB_NAME: apptiempo
  DB_USER: antonio
  DB_PASS: antonio
```

> La tabla consultas se crea automáticamente al arrancar la aplicación. 

---

##  Instalación y ejecución local

### Arrancar la aplicación

```bash
docker compose up -d
```

La primera vez descargará las imágenes de PHP y MariaDB, lo que puede tardar un poco.

### Acceder a la aplicación

```
http://localhost
```

### Parar la aplicación

```bash
docker compose down
```

### Parar y borrar la base de datos

Necesario si cambias las credenciales de la BD:

```bash
docker compose down -v
```

### Entrar a la base de datos

```bash
docker exec -it proyectofinmodulo-db-1 mariadb -u antonio -pantonio apptiempo
```

---

##  Base de datos

### Tabla consultas

```sql
CREATE TABLE IF NOT EXISTS consultas (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    ciudad   VARCHAR(100) NOT NULL,
    pais     VARCHAR(10)  NOT NULL,
    latitud  DECIMAL(9,6) NOT NULL,
    longitud DECIMAL(9,6) NOT NULL,
    tipo     VARCHAR(20)  NOT NULL,
    fecha    DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Valores del campo `tipo`

| Valor | Descripción |
|-------|-------------|
| `actual` | Consulta de tiempo actual |
| `horas` | Consulta de previsión por horas |
| `semana` | Consulta de previsión semanal |

---

##  API de OpenWeatherMap

### Endpoints utilizados

| Endpoint | Uso |
|----------|-----|
| `geo/1.0/direct` | Buscar ciudades por nombre|
| `data/2.5/weather` | Obtener tiempo actual |
| `data/2.5/forecast` | Obtener previsión por horas y semanal |

---

##  Docker

### Servicios

| Servicio | Imagen | Puerto |
|----------|--------|--------|
| `php` | `php:8.2-apache` (personalizada) | `80:80` |
| `db` | `mariadb:10.11` | interno |

### Dockerfile

```dockerfile
FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
```

### Volumen de desarrollo

```yaml
volumes:
  - .:/var/www/html
```

Los cambios en los archivos PHP se ven de forma automatica sin necesidad de reiniciar Docker.

---

##  Despliegue en AWS

### 1. Crear la instancia EC2

1. Accede a **EC2 > Instancias > Lanzar instancia**
2. Elige Debian 
3. Tipo de instancia: **t2.micro**
4. Crea o selecciona un par de claves `.pem` para conectarte por SSH
5. En **Configuración de red**: permite el tráfico SSH (22) y HTTP (80)
6. Lanza la instancia

### 2. Conectarse a la instancia

```bash
ssh -i tu-clave.pem ec2-user@IP_PUBLICA_DE_TU_EC2
```

### 3. Instalar Docker en EC2

```bash
# Instalacion y actualizacion de paquetes de docker
 sudo apt install -y git docker.io docker-compose
 sudo usermod -aG docker $USER
# Contruir dentro del EC2
 docker-compose up -d --build
# Actualizar paquetes
 sudo apt update -y
```
### 4. Preparar el docker-compose.yml para AWS

```yaml
version: '3.8'
services:
 # Servicio PHP con Apache 
  php:
    build: .
    ports:
      - "80:80"           # Puerto 80 del PC apunta al puerto 80 del contenedor
    depends_on:
      - db                # Espera a que la base de datos esté lista
    environment:
      DB_HOST: db
      DB_NAME: apptiempo
      DB_USER: antonio
      DB_PASS: antonio
    volumes:
      - .:/var/www/html   # Los cambios en el código se ven al instante

  # Servicio MariaDB (base de datos)
  db:
    image: mariadb:10.11
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: apptiempo
      MYSQL_USER: antonio
      MYSQL_PASSWORD: antonio
    volumes:
      - datos_db:/var/lib/mysql   # Los datos persisten aunque pares el contenedor

# Volumen para guardar los datos de la base de datos
volumes:
  datos_db:

```

### 5. Subir el docker-compose.yml a EC2

Desde tu PC:

```bash
scp -i tu-clave.pem docker-compose.yml ec2-user@IP_PUBLICA:/home/ec2-user/docker-compose.yml
```

### 6. Arrancar la aplicación en EC2

```bash
ssh -i tu-clave.pem ec2-user@IP_PUBLICA
docker-compose up -d
```

### 7. Abrir el puerto 80 en el Security Group

1. Ve a **EC2 > Instancias** y selecciona tu instancia
2. En la pestaña **Seguridad**, haz clic en el Security Group
3. **Editar reglas de entrada > Agregar regla**
4. Tipo: `HTTP`, Puerto: `80`, Origen: `0.0.0.0/0`
5. Guardar reglas

### 8. Acceder a la aplicación

```
http://IP_PUBLICA_DE_TU_EC2
```

### 9. Actualizar la aplicación en AWS

```bash

# En EC2: descargar la nueva imagen y reiniciar
docker-compose pull
docker-compose up -d
```

---

## 📌 Resumen de comandos útiles

| Comando | Descripción |
|---------|-------------|
| `docker compose up -d` | Arrancar los contenedores |
| `docker compose down` | Parar los contenedores |
| `docker compose down -v` | Parar y borrar la base de datos |
| `docker compose logs -f php` | Ver logs en tiempo real |
| `docker compose ps` | Ver estado de los contenedores |
| `docker build -t user/app .` | Construir la imagen Docker |
| `docker push user/app` | Subir imagen a Docker Hub |
| `docker-compose pull` | Descargar la última imagen en EC2 |

---


