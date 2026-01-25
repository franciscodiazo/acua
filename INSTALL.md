# 📋 Guía de Instalación - Acua

## Requisitos Previos

- **Node.js**: v18 o superior ([Descargar](https://nodejs.org/))
- **npm**: v9 o superior (incluido con Node.js)
- **MySQL**: v8 o superior (o XAMPP con MySQL incluido)
- **Git**: v2.30 o superior ([Descargar](https://git-scm.com/))

## Método 1: Instalador Automático (Windows - Recomendado)

### Paso 1: Ejecutar el instalador

```powershell
# Desde la carpeta del proyecto
.\install.ps1
```

El script automáticamente:
- ✅ Instala dependencias Node.js
- ✅ Genera la base de datos MySQL
- ✅ Ejecuta migraciones Prisma
- ✅ Crea datos de prueba (seed)
- ✅ Inicia el servidor de desarrollo

### Paso 2: Acceder a la aplicación

```
http://localhost:3000
```

---

## Método 2: Instalación Manual

### Paso 1: Clonar el proyecto

```bash
git clone https://github.com/franciscodiazo/Acua.git
cd Acua
```

### Paso 2: Instalar dependencias

```bash
npm install
```

### Paso 3: Configurar base de datos

#### Opción A: Usar XAMPP (Recomendado para Windows)

1. Inicia XAMPP Control Panel
2. Haz clic en "Start" en MySQL
3. Abre el navegador: `http://localhost/phpmyadmin`
4. Crea una nueva base de datos llamada `acua_db`

#### Opción B: Usando MySQL directo

```bash
# Crea la BD desde terminal
mysql -u root -p -e "CREATE DATABASE acua_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Paso 4: Configurar variables de entorno

Crea un archivo `.env` en la raíz del proyecto:

```env
# Base de datos MySQL
DATABASE_URL="mysql://root:@localhost:3306/acua_db"

# Servidor
PORT=3000
NODE_ENV=development

# Email (opcional)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=tu_email@gmail.com
SMTP_PASS=tu_contraseña_app
SMTP_FROM=noreply@acua.com
```

**Variables de BASE DE DATOS según tu configuración:**

- **XAMPP con contraseña**: `mysql://root:contraseña@localhost:3306/acua_db`
- **XAMPP sin contraseña**: `mysql://root:@localhost:3306/acua_db`
- **MySQL remoto**: `mysql://usuario:contraseña@host:puerto/acua_db`

### Paso 5: Generar Prisma Client

```bash
npx prisma generate
```

### Paso 6: Ejecutar migraciones

```bash
npx prisma migrate deploy
```

### Paso 7: Cargar datos iniciales (opcional)

```bash
node scripts/seed.js
```

### Paso 8: Iniciar la aplicación

```bash
npm run dev
```

La aplicación estará disponible en: **http://localhost:3000**

---

## Estructura Base de Datos

La aplicación incluye las siguientes tablas principales:

### 1. **Subscribers** - Suscriptores
```
- id (PK)
- nombre
- apellido
- email
- telefono
- direccion
- matricula
- estado
```

### 2. **Readings** - Lecturas de agua
```
- id (PK)
- subscriberId (FK)
- ciclo
- lecturaActual
- lecturaAnterior
- consumo
- valorUnitario
- valorTotal
- fecha
```

### 3. **Invoices** - Facturas
```
- id (PK)
- readingId (FK)
- numero
- total
- estado
- fecha
- dueDate
```

### 4. **Credits** - Créditos
```
- id (PK)
- subscriberId (FK)
- amount
- description
- createdAt
```

### 5. **CreditPayments** - Pagos de créditos
```
- id (PK)
- creditId (FK)
- amount
- paymentDate
```

### 6. **Users** - Usuarios del sistema
```
- id (PK)
- email
- password
- nombre
- role
```

---

## Verificación de Instalación

Después de instalar, verifica que todo funciona:

```bash
# Prueba la conexión a BD
npx prisma db push

# Inicia el servidor
npm run dev

# En otro terminal, prueba la API
curl http://localhost:3000/api/subscribers?limit=5
```

---

## Solución de Problemas

### ❌ Error: "ECONNREFUSED - MySQL no conecta"

```bash
# Verifica que MySQL está corriendo
# En XAMPP: asegúrate de haber iniciado MySQL
# En Windows cmd:
mysql -u root -p

# Si no funciona, reinicia MySQL
```

### ❌ Error: "Cannot find module 'prisma'"

```bash
# Regenera Prisma Client
npx prisma generate

# O reinstala todo
npm install
npx prisma generate
```

### ❌ Error: "Port 3000 already in use"

```bash
# Encuentra el proceso usando puerto 3000
Get-Process -Id (Get-NetTCPConnection -LocalPort 3000).OwningProcess

# O inicia en otro puerto
PORT=3001 npm run dev
```

### ❌ Base de datos vacía

```bash
# Carga datos de prueba
node scripts/seed.js
```

---

## Comandos Útiles

```bash
# Iniciar en desarrollo
npm run dev

# Construir para producción
npm run build

# Iniciar producción
npm run start

# Ejecutar tests
npm test

# Generar reporte de cobertura
npm run test:coverage

# Limpiar cache
npm run clean

# Regenerar Prisma Client
npx prisma generate

# Abrir Prisma Studio (interfaz gráfica de BD)
npx prisma studio
```

---

## Acceso a la Aplicación

| Módulo | URL | Descripción |
|--------|-----|-------------|
| Dashboard | `http://localhost:3000` | Panel principal |
| Suscriptores | `http://localhost:3000/subscribers` | Gestión de clientes |
| Lecturas | `http://localhost:3000/readings` | Registro de consumos |
| Facturas | `http://localhost:3000/invoices` | Facturas generadas |
| Reportes | `http://localhost:3000/reports` | Reportes financieros |
| Créditos | `http://localhost:3000/credits` | Gestión de créditos |
| Pagos | `http://localhost:3000/payments` | Registro de pagos |

---

## Datos de Prueba

Si ejecutaste `scripts/seed.js`, tendrás:

- **Suscriptores**: 10 registros de prueba
- **Lecturas**: Histórico de consumos
- **Facturas**: Ejemplos de facturas
- **Créditos**: Registros de prueba

---

## Variables de Entorno Completas

```env
# Database
DATABASE_URL="mysql://usuario:contraseña@localhost:3306/acua_db"

# Server
PORT=3000
NODE_ENV=development

# Email Configuration (opcional)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=tu_email@gmail.com
SMTP_PASS=tu_contraseña_app
SMTP_FROM=noreply@acua.com

# API Configuration (opcional)
API_URL=http://localhost:3000
API_TIMEOUT=30000

# Features
ENABLE_EMAIL_NOTIFICATIONS=false
ENABLE_SMS_NOTIFICATIONS=false
ENABLE_INVOICE_PDF=true
```

---

## Soporte

Si tienes problemas:

1. Verifica el archivo `.env` está correctamente configurado
2. Asegúrate que MySQL está corriendo
3. Ejecuta `npm install` nuevamente
4. Limpia cache: `rm -r node_modules package-lock.json && npm install`
5. Consulta los logs en la consola

---

## Próximos Pasos

1. Configura las variables de entorno en `.env`
2. Personaliza la información de la empresa en el Dashboard
3. Importa tus datos de suscriptores
4. Configura las notificaciones por email
5. Inicia la operación normal del sistema

¡Listo! 🚀
