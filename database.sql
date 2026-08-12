-- =========================================================
-- BASE DE DATOS PEJIBAYERA DEL ESTE
-- =========================================================

DROP DATABASE IF EXISTS PejibayeraDelEste;

CREATE DATABASE IF NOT EXISTS PejibayeraDelEste;
USE PejibayeraDelEste;


-- =========================================================
-- USUARIOS
-- =========================================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('Administrador','Cliente','Empresa') NOT NULL,
    estadoUsuario ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
    apellido VARCHAR(50),
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- EMPRESA
-- =========================================================

CREATE TABLE IF NOT EXISTS Empresa (
    cedulaJuridica VARCHAR(20) PRIMARY KEY,
    idUsuario INT NOT NULL UNIQUE,

    CONSTRAINT fk_empresa_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- =========================================================
-- CATEGORIA
-- =========================================================

CREATE TABLE IF NOT EXISTS Categoria (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,
    nombreCategoria VARCHAR(50) NOT NULL
);


-- =========================================================
-- PRESENTACION
-- =========================================================

CREATE TABLE IF NOT EXISTS Presentacion (
    idPresentacion INT AUTO_INCREMENT PRIMARY KEY,
    tipoEmpaque ENUM('Unidad','Caja','Saco','Bolsa','Frasco') NOT NULL,
    peso DECIMAL(10,2) NOT NULL,
    tamaño VARCHAR(50) NOT NULL
);


-- =========================================================
-- PRODUCTO
-- =========================================================

CREATE TABLE IF NOT EXISTS Producto (
    idProducto INT AUTO_INCREMENT PRIMARY KEY,
    nombreProducto VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidadStock INT NOT NULL,
    ventaEmpresarial BOOLEAN NOT NULL,
    idCategoria INT NOT NULL,
    idPresentacion INT NOT NULL,

    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (idCategoria)
        REFERENCES Categoria(idCategoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_producto_presentacion
        FOREIGN KEY (idPresentacion)
        REFERENCES Presentacion(idPresentacion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- =========================================================
-- CARRITO
-- =========================================================

CREATE TABLE IF NOT EXISTS Carrito (
    idCarrito INT AUTO_INCREMENT PRIMARY KEY,
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechaEntrega DATE NULL,
    observaciones VARCHAR(255) NULL,
    estadoCarrito ENUM('Activo','Finalizado','Abandonado')
        NOT NULL DEFAULT 'Activo',
    idUsuario INT NOT NULL,

    CONSTRAINT fk_carrito_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- =========================================================
-- DETALLE CARRITO
-- =========================================================

CREATE TABLE IF NOT EXISTS DetalleCarrito (
    idDetalleCarrito INT AUTO_INCREMENT PRIMARY KEY,
    cantidadCarrito INT NOT NULL,
    idCarrito INT NOT NULL,
    idProducto INT NOT NULL,

    CONSTRAINT fk_detallecarrito_carrito
        FOREIGN KEY (idCarrito)
        REFERENCES Carrito(idCarrito)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detallecarrito_producto
        FOREIGN KEY (idProducto)
        REFERENCES Producto(idProducto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- =========================================================
-- PEDIDO
-- =========================================================

CREATE TABLE IF NOT EXISTS Pedido (
    idPedido INT AUTO_INCREMENT PRIMARY KEY,
    fechaPedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fechaEntrega DATE NULL,
    observaciones VARCHAR(255) NULL,
    estadoPedido ENUM(
        'Pendiente',
        'En preparación',
        'Enviado',
        'Entregado',
        'Cancelado'
    ) NOT NULL DEFAULT 'Pendiente',
    tipoPedido ENUM('Individual','Empresa') NOT NULL,
    pedidoTotal DECIMAL(10,2) NOT NULL,
    idUsuario INT NOT NULL,

    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- =========================================================
-- DETALLE PEDIDO
-- =========================================================

CREATE TABLE IF NOT EXISTS DetallePedido (
    idDetallePedido INT AUTO_INCREMENT PRIMARY KEY,
    precioUnitario DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    idPedido INT NOT NULL,
    idProducto INT NOT NULL,

    CONSTRAINT fk_detallepedido_pedido
        FOREIGN KEY (idPedido)
        REFERENCES Pedido(idPedido)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detallepedido_producto
        FOREIGN KEY (idProducto)
        REFERENCES Producto(idProducto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- =========================================================
-- USUARIO ADMIN :admin@example.com
-- Contraseña: admin123
-- =========================================================

INSERT INTO users (
    nombre,
    email,
    password,
    rol
)
VALUES (
    'Admin',
    'admin@example.com',
    '$2y$10$ona2Mi3HzulIVmBW4Bki6uUhncN1NiM.ZjH4tmqH9K93..DVw9PnG',
    'Administrador'
);


-- =========================================================
-- USUARIO CLIENTE
-- Contraseña: jose
-- =========================================================

INSERT INTO users (
    nombre,
    email,
    password,
    rol
)
VALUES (
    'Jose',
    'jose@example.com',
    '$2y$10$WCLO4aZiRVLL22s4AQyucugMbo0r7APKEi0lrbkK8BFc2H0PoQ2A6',
    'Cliente'
);


-- =========================================================
-- USUARIO EMPRESA
-- Contraseña: empanada
-- =========================================================

INSERT INTO users (
    nombre,
    email,
    password,
    rol
)
VALUES (
    'Empanada',
    'empanada@example.com',
    '$2y$10$aH6kpTkJrBjy0IBlspjb3uxigxwSRX5Mi44/G2dhpHaLhboABLafS',
    'Empresa'
);


-- =========================================================
-- EMPRESA DEL USUARIO 3
-- =========================================================

INSERT INTO Empresa (
    cedulaJuridica,
    idUsuario
)
VALUES (
    '0001',
    3
);


-- =========================================================
-- CATEGORIAS
-- =========================================================

INSERT INTO Categoria (nombreCategoria)
VALUES ('Pejibaye');

INSERT INTO Categoria (nombreCategoria)
VALUES ('Harina');


-- =========================================================
-- PRESENTACIONES
-- =========================================================

INSERT INTO Presentacion (
    tipoEmpaque,
    peso,
    tamaño
)
VALUES (
    'Unidad',
    '340',
    'Grande'
);

INSERT INTO Presentacion (
    tipoEmpaque,
    peso,
    tamaño
)
VALUES (
    'Caja',
    '900',
    'Lote Mediano'
);

INSERT INTO Presentacion (
    tipoEmpaque,
    peso,
    tamaño
)
VALUES (
    'Bolsa',
    '50',
    'Pequeño'
);


-- =========================================================
-- PRODUCTO PARA CLIENTE
-- =========================================================

INSERT INTO Producto (
    nombreProducto,
    descripcion,
    precio,
    cantidadStock,
    ventaEmpresarial,
    idCategoria,
    idPresentacion
)
VALUES (
    'Pejibaye Grande',
    'Recién cosechados de nuestra finca, ideales para cocinar',
    '2200',
    '24',
    FALSE,
    1,
    1
);


-- =========================================================
-- PRODUCTO PARA EMPRESA
-- =========================================================

INSERT INTO Producto (
    nombreProducto,
    descripcion,
    precio,
    cantidadStock,
    ventaEmpresarial,
    idCategoria,
    idPresentacion
)
VALUES (
    'Lote Mediano de Pejibayes Grandes',
    'Recién cosechados de nuestra finca, ideales para cocinar',
    '7500',
    '5',
    TRUE,
    1,
    2
);


-- =========================================================
-- PRODUCTO PARA CLIENTE
-- =========================================================

INSERT INTO Producto (
    nombreProducto,
    descripcion,
    precio,
    cantidadStock,
    ventaEmpresarial,
    idCategoria,
    idPresentacion
)
VALUES (
    'Harina de Pejibaye',
    'Lleve lo a su mesa, comer saludable es comer feliz con nuestra harina',
    '750',
    '15',
    FALSE,
    2,
    3
);


-- =========================================================
-- COMPROBACION
-- =========================================================

SELECT * FROM users;
SELECT * FROM Categoria;
SELECT * FROM Presentacion;
SELECT * FROM Producto;
SELECT * FROM Carrito;
SELECT * FROM DetalleCarrito;
SELECT * FROM Pedido;
SELECT * FROM DetallePedido;

SHOW TABLES;

 -- mas stock
  -- para ver la tabla 
 -- SELECT idProducto, nombreProducto, cantidadStock
-- FROM Producto;

-- desactivar modo seguro si es que lo pide

-- SET SQL_SAFE_UPDATES = 0;

 -- ponemos mas stock 
 -- UPDATE Producto
-- SET cantidadStock = 20
-- WHERE nombreProducto = 'Lote Mediano de Pejibayes Grandes';

-- vemos si se actualizo

SELECT idProducto, nombreProducto, cantidadStock, ventaEmpresarial
FROM Producto;
 

