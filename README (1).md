# 🌤 Aplicación del Tiempo

Aplicación web desarrollada en PHP puro siguiendo el patrón de arquitectura **MVC** (Model-View-Controller). Permite consultar el tiempo atmosférico de cualquier ciudad del mundo usando la API de OpenWeatherMap.

---

## 📋 Funcionalidades

- Búsqueda de ciudades por nombre con geocodificación
- Tiempo actual (temperatura, viento, humedad, presión, visibilidad...)
- Previsión por horas (próximas 24h)
- Previsión semanal (5 días con máximas, mínimas y lluvia)
- Gráficas interactivas con Chart.js
- Historial de consultas guardado en base de datos MariaDB
- Patrón DAO para el acceso a datos

---

## 🏗 Arquitectura MVC

El proyecto sigue el patrón Modelo-Vista-Controlador de forma sencilla:

| Capa | Carpeta | Responsabilidad |
|------|---------|-----------------|
| **Model** | `models/` | Acceso a la base de datos (patrón DAO) |
| **View** | `views/` | HTML que ve el usuario, sin lógica de negocio |
| **Controller** | `controllers/` | Une Model y View, llama a la API y prepara los datos |

Los archivos de la raíz (`acceso-*.php`) son los **puntos de entrada** que recibe el navegador. Cada uno instancia su Controller correspondiente y lo ejecuta.

---

## 📁 Estructura de archivos

```
Aplicacion_del_Tiempo_MVC/
  ├── index.php                          ← entrada: buscador
  ├── acceso-tiempo-ahora.php            ← entrada: tiempo actual
  ├── acceso-prevision-horas.php         ← entrada: previsión horas
  ├── acceso-prevision-semanal.php       ← entrada: previsión semanal
  ├── acceso-consultas-realizadas.php    ← entrada: historial
  ├── config.php                         ← API key y constantes
  ├── Dockerfile
  ├── docker-compose.yml
  ├── controllers/
  │     ├── BuscadorController.php
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

## ⚙️ Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y en ejecución
- Cuenta gratuita en [openweathermap.org](https://openweathermap.org/api) para obtener una API Key
- Conexión a internet (la app llama a la API en tiempo real)

---

## 🔧 Configuración

### API Key

Abre `config.php` y sustituye el valor por tu clave personal:

```php
define('API_KEY', 'TU_API_KEY_AQUI');
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

> La tabla `consultas` se crea automáticamente al arrancar la aplicación. No es necesario ejecutar ningún SQL manualmente.

---

## 🚀 Instalación y ejecución local

### Arrancar la aplicación

```bash
docker compose up -d
```

La primera vez descargará las imágenes de PHP y MariaDB, lo que puede tardar unos minutos.

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

### Ver logs en tiempo real

```bash
docker compose logs -f php
```

### Entrar a la base de datos

```bash
docker exec -it aplicacion_del_tiempo_mvc-db-1 mariadb -u antonio -pantonio apptiempo
```

---

## 🗄 Base de datos

### Tabla `consultas`

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

## 🌐 API de OpenWeatherMap

### Endpoints utilizados

| Endpoint | Uso |
|----------|-----|
| `geo/1.0/direct` | Buscar ciudades por nombre (geocodificación) |
| `data/2.5/weather` | Obtener tiempo actual |
| `data/2.5/forecast` | Obtener previsión por horas y semanal |

### Plan gratuito

- 60 llamadas por minuto
- Previsión máxima de 5 días
- Actualización cada 10 minutos

---

## 🐳 Docker

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

Los cambios en los archivos PHP se reflejan inmediatamente sin necesidad de reiniciar Docker.

---

## ☁️ Despliegue en AWS

### 1. Preparar la imagen Docker

Necesitas una cuenta en [hub.docker.com](https://hub.docker.com). Construye y sube la imagen desde tu PC:

```bash
# Construir la imagen
docker build -t tuusuario/weatherapp:latest .

# Iniciar sesión en Docker Hub
docker login

# Subir la imagen
docker push tuusuario/weatherapp:latest
```

### 2. Crear la instancia EC2

1. Ve a [console.aws.amazon.com](https://console.aws.amazon.com)
2. Accede a **EC2 > Instancias > Lanzar instancia**
3. Elige **Amazon Linux 2023** (capa gratuita)
4. Tipo de instancia: **t2.micro** (capa gratuita)
5. Crea o selecciona un par de claves `.pem` para conectarte por SSH
6. En **Configuración de red**: permite el tráfico SSH (22) y HTTP (80)
7. Lanza la instancia

### 3. Conectarse a la instancia

```bash
ssh -i tu-clave.pem ec2-user@IP_PUBLICA_DE_TU_EC2
```

### 4. Instalar Docker en EC2

```bash
# Actualizar paquetes
sudo yum update -y

# Instalar Docker
sudo yum install docker -y

# Iniciar Docker y habilitarlo al arranque
sudo systemctl start docker
sudo systemctl enable docker

# Añadir tu usuario al grupo docker
sudo usermod -aG docker ec2-user

# Instalar Docker Compose
sudo curl -L https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m) -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Cerrar sesión y volver a entrar para aplicar el grupo
exit
```

### 5. Preparar el docker-compose.yml para AWS

Crea un `docker-compose.yml` para producción. La diferencia con el local es que usa `image:` en vez de `build:`:

```yaml
version: '3.8'
services:
  php:
    image: tuusuario/weatherapp:latest
    ports:
      - "80:80"
    depends_on:
      - db
    environment:
      DB_HOST: db
      DB_NAME: apptiempo
      DB_USER: antonio
      DB_PASS: antonio
  db:
    image: mariadb:10.11
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: apptiempo
      MYSQL_USER: antonio
      MYSQL_PASSWORD: antonio
    volumes:
      - datos_db:/var/lib/mysql
volumes:
  datos_db:
```

### 6. Subir el docker-compose.yml a EC2

Desde tu PC:

```bash
scp -i tu-clave.pem docker-compose.yml ec2-user@IP_PUBLICA:/home/ec2-user/docker-compose.yml
```

### 7. Arrancar la aplicación en EC2

```bash
ssh -i tu-clave.pem ec2-user@IP_PUBLICA
docker-compose up -d
```

### 8. Abrir el puerto 80 en el Security Group

1. Ve a **EC2 > Instancias** y selecciona tu instancia
2. En la pestaña **Seguridad**, haz clic en el Security Group
3. **Editar reglas de entrada > Agregar regla**
4. Tipo: `HTTP`, Puerto: `80`, Origen: `0.0.0.0/0`
5. Guardar reglas

### 9. Acceder a la aplicación

```
http://IP_PUBLICA_DE_TU_EC2
```

### 10. Actualizar la aplicación en AWS

```bash
# En tu PC: reconstruir y subir la imagen
docker build -t tuusuario/weatherapp:latest .
docker push tuusuario/weatherapp:latest

# En EC2: descargar la nueva imagen y reiniciar
docker-compose pull
docker-compose up -d
```

---

## 📦 Subir a GitHub

### 1. Crear .gitignore

Antes de subir el código, crea un `.gitignore` en la raíz:

```
.env
.DS_Store
Thumbs.db
```

> ⚠️ **Importante:** No subas tu API Key real. Antes de hacer commit, pon un valor de ejemplo en `config.php`:
> ```php
> define('API_KEY', 'TU_API_KEY_AQUI');
> ```

### 2. Subir el código

```bash
# Inicializar el repositorio local
git init

# Añadir todos los archivos
git add .

# Primer commit
git commit -m "Aplicacion del tiempo MVC con Docker"

# Conectar con GitHub
git remote add origin https://github.com/tuusuario/aplicacion-del-tiempo.git

# Subir
git push -u origin main
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

## ✅ Estado del proyecto

| Criterio | Estado |
|----------|--------|
| Búsqueda por ciudad | ✅ Completado |
| Tiempo actual | ✅ Completado |
| Previsión por horas | ✅ Completado |
| Previsión semanal | ✅ Completado |
| Gráficas con Chart.js | ✅ Completado |
| Base de datos MariaDB | ✅ Completado |
| Patrón DAO | ✅ Completado |
| Arquitectura MVC | ✅ Completado |
| Docker | ✅ Completado |
| Subida a AWS | ⏳ Pendiente |
| Subida a GitHub | ⏳ Pendiente |
