# SRN Empanadas — Quick Start (Docker)


## Requisitos
- Docker / Docker Compose

## Servicios y Puertos
- **API** (Node): http://localhost:3000
- **UI** (CodeIgniter 4): http://localhost:8080
- **phpMyAdmin**: http://localhost:8081 (Host: `db`, User: `emp_user`, Pass: `emp_pass`)
- **MySQL**: Host `db` (red interna de Docker), puerto interno 3306



## ENV variables
Copia `.env.example` a `.env`:
```
PORT=3000
DB_HOST=db
DB_USER=emp_user
DB_PASSWORD=emp_pass
DB_NAME=empanadas_db
CORS_ORIGIN=http://localhost:8080
# (opcional) activa seguridad por API key:
API_KEY=
```

## Levantar todo
```bash
cp api/.env.example api/.env
docker compose up -d --build
```



## Tests del backend
```bash
cd api
npm install
npm test
```
> Los tests corren con Jest + Supertest (ESM) y mock de `db` por módulo.
