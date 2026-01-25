-- ==================================================
-- ACUA - Base de Datos MySQL
-- Sistema de Gestión de Acueducto Rural
-- ==================================================

-- Crear BD
CREATE DATABASE IF NOT EXISTS acua_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE acua_db;

-- Tabla: Usuarios (Roles: admin, operador, viewer)
CREATE TABLE IF NOT EXISTS User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    role VARCHAR(50) DEFAULT 'operador',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla: Configuración del Sistema
CREATE TABLE IF NOT EXISTS Config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    value VARCHAR(255) NOT NULL,
    description TEXT,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla: Suscriptores
CREATE TABLE IF NOT EXISTS Subscriber (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    telefono VARCHAR(20),
    direccion TEXT NOT NULL,
    matricula VARCHAR(50) NOT NULL UNIQUE,
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_matricula (matricula),
    INDEX idx_estado (estado),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB;

-- Tabla: Lecturas de Consumo
CREATE TABLE IF NOT EXISTS Reading (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriberId INT NOT NULL,
    ciclo VARCHAR(20) NOT NULL,
    lecturaAnterior INT NOT NULL DEFAULT 0,
    lecturaActual INT NOT NULL,
    consumo INT GENERATED ALWAYS AS (lecturaActual - lecturaAnterior) STORED,
    valorUnitario DECIMAL(12,2) NOT NULL DEFAULT 0,
    valorTotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    fecha DATE NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriberId) REFERENCES Subscriber(id) ON DELETE CASCADE,
    INDEX idx_subscriberId (subscriberId),
    INDEX idx_fecha (fecha),
    INDEX idx_ciclo (ciclo)
) ENGINE=InnoDB;

-- Tabla: Facturas
CREATE TABLE IF NOT EXISTS Invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    readingId INT NOT NULL,
    numero VARCHAR(50) NOT NULL UNIQUE,
    total DECIMAL(12,2) NOT NULL,
    estado ENUM('pendiente', 'pagada', 'cancelada') DEFAULT 'pendiente',
    fecha DATE NOT NULL,
    dueDate DATE NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (readingId) REFERENCES Reading(id) ON DELETE CASCADE,
    INDEX idx_numero (numero),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB;

-- Tabla: Créditos
CREATE TABLE IF NOT EXISTS Credit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriberId INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriberId) REFERENCES Subscriber(id) ON DELETE CASCADE,
    INDEX idx_subscriberId (subscriberId),
    INDEX idx_createdAt (createdAt)
) ENGINE=InnoDB;

-- Tabla: Pagos de Créditos
CREATE TABLE IF NOT EXISTS CreditPayment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creditId INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    paymentDate DATE NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creditId) REFERENCES Credit(id) ON DELETE CASCADE,
    INDEX idx_creditId (creditId),
    INDEX idx_paymentDate (paymentDate)
) ENGINE=InnoDB;

-- ==================================================
-- DATOS INICIALES DE CONFIGURACIÓN
-- ==================================================

INSERT IGNORE INTO Config (key, value, description) VALUES
('EMPRESA_NOMBRE', 'Acua - Acueducto Rural', 'Nombre de la empresa'),
('EMPRESA_NIT', '123456789', 'NIT de la empresa'),
('EMPRESA_TELEFONO', '+57 (1) 1234567', 'Teléfono de la empresa'),
('EMPRESA_EMAIL', 'info@acua.com', 'Email de la empresa'),
('EMPRESA_DIRECCION', 'Calle Principal 123', 'Dirección de la empresa'),
('TARIFA_BASICA', '25000', 'Tarifa básica en COP'),
('CONSUMO_UMBRAL', '40', 'Umbral de consumo en m³'),
('PRECIO_UNITARIO', '1500', 'Precio por m³ adicional en COP'),
('CICLO_FACTURACION', '30', 'Ciclo de facturación en días'),
('MORA_DIAS', '15', 'Días de vencimiento para mora'),
('SUSPENSION_DIAS', '45', 'Días para suspensión por mora');

-- ==================================================
-- CREAR ÍNDICES PARA MEJOR RENDIMIENTO
-- ==================================================

CREATE INDEX idx_reading_subscriber_fecha ON Reading(subscriberId, fecha DESC);
CREATE INDEX idx_invoice_subscriber_estado ON Invoice(estado, fecha DESC);
CREATE INDEX idx_credit_subscriber ON Credit(subscriberId, createdAt DESC);

-- ==================================================
-- FIN DE INSTALACIÓN
-- ==================================================
-- La BD está lista. Ejecuta las migraciones de Prisma:
-- npx prisma migrate deploy
-- ==================================================
