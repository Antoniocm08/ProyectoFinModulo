#  Api del Tiempo

Aplicación web desarrollada en PHP puro siguiendo el patrón de arquitectura **MVC**. Permite consultar el tiempo atmosférico de cualquier ciudad del mundo usando la API de OpenWeatherMap de forma rápida.

---

##  Funcionalidades

- Búsqueda de ciudades por nombre
- Tiempo actual
- Previsión por horas
- Previsión semanal
- Gráficas interactivas con Chart.js
- Historial de consultas guardado en base de datos
- Utilización del DAO para el acceso a datos

---

##  Arquitectura MVC

El proyecto sigue el patrón Modelo-Vista-Controlador:

| Capa | Carpeta | Responsabilidad |
|------|---------|-----------------|
| **Model** | `models/` | Acceso a la base de datos |
| **View** | `views/` | HTML que ve el usuario |
| **Controller** | `controllers/` | Une Model y View, llama a la API y prepara los datos |

---

##  Estructura de archivos

```
Aplicacion_del_Tiempo_MVC/
  ├── index.php                          ← Página principal de la aplicación
  ├── acceso-tiempo-ahora.php            ← Muestra el tiempo actual de la ciudad buscada
  ├── acceso-prevision-horas.php         ← Muestra la previsión del tiempo por horas
  ├── acceso-prevision-semanal.php       ← Muestra la previsión para los próximos días
  ├── acceso-consultas-realizadas.php    ← Historial de búsquedas realizadas
  ├── config.php                         ← API key y configuración
  ├── Dockerfile                         ← Define cómo se construye el contenedor
  ├── docker-compose.yml                 ← Permite ejecutar toda la aplicación con Docker
  ├── controllers/
  │     ├── BuscadorController.php       ← Procesa la búsqueda de ciudades
  │     ├── ActualController.php         ← Procesa la hora actual
  │     ├── HorasController.php          ← Procesa las proximas horas
  │     ├── SemanaController.php         ← Procesa el tiempo semanal
  │     └── HistorialController.php      ← Procesa el historial de busqueda
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
- Craer cuenta en [openweathermap.org](https://openweathermap.org/api) para obtener una API Key

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

> La tabla `consultas` se crea automáticamente al arrancar la aplicación.

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
docker exec -it aplicacion_del_tiempo_mvc-db-1 mariadb -u antonio -pantonio apptiempo
```

---

##  Base de datos

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

##  API de OpenWeatherMap

### Endpoints utilizados

| Endpoint | Uso |
|----------|-----|
| `geo/1.0/direct` | Buscar ciudades por nombre |
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

Los cambios en los archivos PHP se ven de forma automática sin necesidad de reiniciar Docker.

---

##  Despliegue en AWS

### 1. Crear la instancia EC2

1. Accede a **EC2 > Instancias > Lanzar instancia**
2. Elige **Debian**
3. Tipo de instancia: **t3.micro**
4. Crea o selecciona un par de claves `.pem` para conectarte por SSH
5. En **Configuración de red**: permite el tráfico SSH (22) y HTTP (80)
6. Lanza la instancia

### 2. Conectarse a la instancia

```bash
ssh -i labsuser.pem admin@52.21.211.88
```

### 3. Instalar Docker y Git en EC2

```bash
# Actualizar paquetes
sudo apt update -y

# Instalar Git, Docker y Docker Compose
sudo apt install -y git docker.io docker-compose

# Añadir tu usuario al grupo docker (para no usar sudo)
sudo usermod -aG docker $USER

# Cerrar sesión y volver a entrar para aplicar el grupo
exit
```

Vuelve a conectarte:

```bash
ssh -i labsuser.pem admin@52.21.211.88
```

### 4. Clonar el repositorio en EC2

```bash
git clone https://github.com/antoniocm8/aplicacion-del-tiempo.git
cd aplicacion-del-tiempo
```

### 5. Arrancar la aplicación

```bash
docker-compose up -d --build
```

### 6. Actualizar la aplicación en AWS

Cuando hagas cambios en el código, súbelos a GitHub desde tu PC y luego en EC2:

```bash
cd aplicacion-del-tiempo
git pull
docker-compose up -d --build
```

---
### 7. Dominio utilizado y ip elastica utilizada
http://antoniotiempo.ddns.net/
http://52.21.211.88/
## 📌 Resumen de comandos útiles

| Comando | Descripción |
|---------|-------------|
| `docker compose up -d` | Arrancar los contenedores |
| `docker compose down` | Parar los contenedores |
| `docker compose down -v` | Parar y borrar la base de datos |
| `docker compose logs -f php` | Ver logs en tiempo real |
| `docker compose ps` | Ver estado de los contenedores |
| `docker-compose up -d --build` | Reconstruir y arrancar en EC2 |
| `git pull` | Descargar los últimos cambios del repositorio |

---


