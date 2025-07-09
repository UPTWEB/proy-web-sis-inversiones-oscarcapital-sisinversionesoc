# 🚀 Proyecto PHP MVC con Docker

Este proyecto está configurado para ejecutarse en Docker usando `docker-compose`.

---

## ✅ Requisitos

- Tener [Docker](https://docs.docker.com/get-docker/) instalado.
- Tener Docker Compose (ya viene con Docker Desktop).

---

## 🛠️ Pasos para ejecutar el proyecto

1. Clona el repositorio:

   ```bash
   git clone https://github.com/nkmelndz/Sistema_de_Inversiones.git
   cd Sistema_de_Inversiones

2. Levanta el contenedor:

   ```bash
   docker-compose up --build

3. Abre tu navegador y visita:
   
   http://localhost:8080

## ♻️ Cambios en el código

- El código está montado con volumen, así que los cambios se reflejan automáticamente.
- Si no ves los cambios, prueba:
  - Forzar recarga: `Ctrl + Shift + R`
  - Reiniciar contenedor: `docker-compose restart`

---

## 📌 Importante

Si cambias el `Dockerfile` o archivos que se copian dentro del contenedor, **siempre** ejecuta:

```bash
docker-compose up --build
