# ⚡ Inicio Rápido - Acua

## 5 Minutos para tener Acua corriendo

### 🎯 En Windows (más fácil)

1. **Abre PowerShell** en la carpeta del proyecto

2. **Ejecuta el instalador:**
   ```powershell
   .\install.ps1
   ```
   
   *Esto hace TODO automáticamente:*
   - ✅ Instala Node.js packages
   - ✅ Configura Prisma
   - ✅ Crea base de datos
   - ✅ Ejecuta migraciones
   - ✅ Carga datos de prueba

3. **Espera a que termine** (suena música, es broma... espera unos 2-3 minutos)

4. **Inicia el servidor:**
   ```powershell
   npm run dev
   ```

5. **Abre en navegador:**
   ```
   http://localhost:3000
   ```

---

### 🐧 En Linux/Mac

```bash
# 1. Instala dependencias
npm install

# 2. Genera Prisma Client
npx prisma generate

# 3. Crea BD (si no existe)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS acua_db CHARACTER SET utf8mb4;"

# 4. Ejecuta migraciones
npx prisma migrate deploy

# 5. Carga datos iniciales
node scripts/seed.js

# 6. Inicia servidor
npm run dev
```

---

## 🚨 Si algo falla

### "MySQL connection refused"
```
✅ Solución: Inicia MySQL (XAMPP → MySQL → Start)
```

### "Port 3000 already in use"
```
✅ Solución: npm run dev con otro puerto
PORT=3001 npm run dev
```

### "Prisma Client not found"
```
✅ Solución: npx prisma generate && npm install
```

### "No data in database"
```
✅ Solución: node scripts/seed.js
```

---

## 📍 URLs Principales

| Módulo | URL |
|--------|-----|
| 🏠 Dashboard | http://localhost:3000 |
| 👥 Suscriptores | http://localhost:3000/subscribers |
| 💧 Lecturas | http://localhost:3000/readings |
| 🧾 Facturas | http://localhost:3000/invoices |
| 📊 Reportes | http://localhost:3000/reports |
| 💰 Créditos | http://localhost:3000/credits |
| 💳 Pagos | http://localhost:3000/payments |

---

## 🔑 Credenciales de Prueba

Si cargaste datos iniciales (`node scripts/seed.js`), tendrás:

- **10 suscriptores de prueba** (Juan Pérez, María García, etc.)
- **Lecturas de consumo** para el último mes
- **Facturas generadas** listas para ver en PDF
- **Créditos y pagos** para probar

---

## 📝 Configuración Rápida

### Cambiar puerto
```bash
PORT=8000 npm run dev
```

### Cambiar BD
Edita `.env`:
```env
DATABASE_URL="mysql://usuario:pass@host:puerto/nombre_bd"
```

### Resetear BD (desarrollo solo)
```bash
npx prisma migrate reset
```

---

## 🎓 Próximos Pasos

1. Lee [INSTALL.md](./INSTALL.md) para instalación detallada
2. Lee [README.md](./README.md) para info del proyecto
3. Explora el dashboard
4. Crea tu primer suscriptor
5. Registra tu primera lectura

---

## 💡 Tips

- **Prisma Studio** (ver BD gráficamente):
  ```bash
  npx prisma studio
  ```

- **Ver logs detallados**:
  ```bash
  npm run dev -- --debug
  ```

- **Construir para producción**:
  ```bash
  npm run build
  npm run start
  ```

---

## 🆘 Ayuda

- Revisa los logs en la consola
- Verifica que MySQL está corriendo
- Asegúrate que el puerto 3000 está disponible
- Contacta al desarrollador: franciscojdiazo@gmail.com

---

**¡Ya está! 🚀 Acua está listo para usar**
