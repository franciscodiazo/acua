# 💧 Acua - Sistema de Gestión de Acueducto Rural

[![Next.js](https://img.shields.io/badge/Next.js-14.0-black?logo=next.js)](https://nextjs.org/)
[![Node.js](https://img.shields.io/badge/Node.js-18+-green?logo=node.js)](https://nodejs.org/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql)](https://www.mysql.com/)
[![Prisma](https://img.shields.io/badge/Prisma-5.0-2D3748?logo=prisma)](https://www.prisma.io/)
[![License](https://img.shields.io/badge/License-Privado-red)]()
[![E2E tests](https://github.com/franciscodiazo/acua/actions/workflows/e2e.yml/badge.svg)](https://github.com/franciscodiazo/acua/actions/workflows/e2e.yml)

---

## 📖 Descripción

**Acua** es un sistema completo de gestión para acueductos rurales que permite administrar suscriptores, lecturas de consumo, facturación automática, pagos y generar reportes financieros detallados.

## 🚀 Instalación Rápida

### Opción A: Instalador Automático (Windows - Recomendado)
```powershell
.\install.ps1
```

### Opción B: Instalación Manual
```bash
npm install
npx prisma generate
npx prisma migrate deploy
node scripts/seed.js
npm run dev
```

Pruebas unitarias:
- Ejecutar tests (Jest):
  - npm test
- Las pruebas unitarias mockean `lib/prisma` para validar la paginación y los endpoints (GET `/api/subscribers`, GET/POST `/api/readings`, GET `/api/invoices`, GET `/api/credits`).

Endpoints principales (API):
- POST /api/subscribers -> crear suscriptor
- GET /api/subscribers -> listar
- POST /api/readings -> ingresar lectura (calcula consumo y crea factura)
- GET /api/invoices -> listar facturas (incluye lectura y suscriptor)
- GET /api/invoices/:id -> ver factura
- GET /api/invoices/:id/pdf -> descargar factura en PDF
- POST /api/invoices/:id/send-email -> enviar factura por correo (body: { "to": "correo@ejemplo.com" } opcional)
- POST /api/credits -> agregar crédito a suscriptor
- GET /api/credits -> listar créditos

Páginas nuevas:
- `/invoices` -> lista de facturas
- `/invoices/[id]` -> detalle de factura (marcar como facturado, descargar PDF, enviar por correo)
- `/credits` -> lista de créditos
- `/credits/new` -> crear crédito

Configurar envío de correo (añadir en `.env`):

SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, FROM_EMAIL, BASE_URL

Ejemplo: en `.env`:

SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=usuario
SMTP_PASS=supersecreto
FROM_EMAIL=noreply@midominio.com
BASE_URL=http://localhost:3000

Plantilla de correo:
- La plantilla HTML se encuentra en `lib/emailTemplates.js`. Puedes editar `generateInvoiceEmailHTML` para adaptar el contenido y estilos del correo (logo, texto, enlaces, etc.).

Nota: si no hay correo en el suscriptor, la UI pedirá un correo manualmente para enviar la factura.

Reglas de negocio 🔧:
- Si consumo <= THRESHOLD -> **tarifa básica** (BASIC_TARIFF)
- Si consumo > THRESHOLD -> **tarifa básica + UNIT_PRICE * (consumo - THRESHOLD)**

Variables en `.env` (valores por defecto en el repo):
- BASIC_TARIFF=25000
- THRESHOLD=40
- UNIT_PRICE=1500

Ejemplo de uso:
- Crear suscriptor (POST /api/subscribers): { "matricula":"A123", "apellidos":"Pérez", "nombres":"Ana" }
- Tomar lectura (POST /api/readings): { "matricula":"A123", "contador":"CNT-01", "lecturaAnterior": 100, "lecturaActual": 145 }

Notas:
- Base de datos: MySQL (`acuarius_db`). Si usas el entorno sugerido, ejecuta:

  mysql -u root -e "CREATE DATABASE IF NOT EXISTS acuarius_db;"

  y configura `DATABASE_URL` en `.env` como:

  DATABASE_URL="mysql://root@localhost:3306/acuarius_db"

  Alternativamente, puedes ejecutar el script PowerShell que crea la base y aplica migraciones:

  powershell -ExecutionPolicy Bypass -File .\scripts\setup-db.ps1

- Para ver facturas, visitar: `/api/invoices`
