# SRN Empanadas

Backend API (Node.js + Express + MySQL) con Docker. phpMyAdmin incluido.

## Ramas
- `dev` (por defecto, desarrollo)
- `main` (producción)

## Requisitos
- Docker y Docker Compose
- Git

## Puertos
- API: `http://localhost:3000`
- phpMyAdmin: `http://localhost:8081` (Host: `db`, User: `emp_user`, Pass: `emp_pass`)

## Inicio rápido
```bash
cp api/.env.example api/.env
docker compose up -d --build
