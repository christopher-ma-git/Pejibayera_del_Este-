-- DROP DATABASE IF EXISTS PejibayeraDelEste;

CREATE DATABASE IF NOT EXISTS PejibayeraDelEste;
USE PejibayeraDelEste;

--Implementada con Exito
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

-- Implementada con Exito
CREATE TABLE IF NOT EXISTS Empresa (
    cedulaJuridica VARCHAR(20) PRIMARY KEY,
    idUsuario INT NOT NULL UNIQUE,
    CONSTRAINT fk_empresa_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- Implementada (NO es NECESARIA para la BD) <no funcional>
CREATE TABLE IF NOT EXISTS MetodoPagoUsuario (
    idMetodoPago INT AUTO_INCREMENT PRIMARY KEY,
    numeroTarjeta VARCHAR(20) NOT NULL,
    fechaVencimiento DATE NOT NULL,
    pin VARCHAR(4) NOT NULL,
    idUsuario INT NOT NULL,
    CONSTRAINT fk_metodopago_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Implementada con Exito
CREATE TABLE IF NOT EXISTS Categoria (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,
    nombreCategoria VARCHAR(50) NOT NULL
);

-- Implementada con Exito
CREATE TABLE IF NOT EXISTS Presentacion (
    idPresentacion INT AUTO_INCREMENT PRIMARY KEY,
    tipoEmpaque ENUM('Unidad','Caja','Saco', 'Bolsa', 'Frasco') NOT NULL,
    peso DECIMAL(10,2) NOT NULL,
    tamaño VARCHAR(50) NOT NULL
);

-- Implementada con Exito
CREATE TABLE IF NOT EXISTS Producto (
    idProducto INT AUTO_INCREMENT PRIMARY KEY,
    nombreProducto VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidadStock INT NOT NULL, --Amazon muestra las unidades que quedan, presiona a la gente a comprar
    ventaEmpresarial BOOLEAN NOT NULL, --sirve para saber que productos mostrar (producto para cliente 0, producto para empresa 1)
    idCategoria INT NOT NULL,  
    idPresentacion INT NOT NULL, --ordena mejor los productos
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

--No implementada (Admin) <en cola>
CREATE TABLE IF NOT EXISTS InventarioLote (
    idInventario INT AUTO_INCREMENT PRIMARY KEY,
    fechaIngreso DATE NOT NULL,
    fechaVencimiento DATE NOT NULL,
    cantidadCosecha INT NOT NULL,
    cantidadProducción INT NOT NULL,
    idProducto INT NOT NULL,
    CONSTRAINT fk_lote_producto
        FOREIGN KEY (idProducto)
        REFERENCES Producto(idProducto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

--No implementada (Admin) <en cola>
CREATE TABLE IF NOT EXISTS MovimientoStock (
    idMovimiento INT AUTO_INCREMENT PRIMARY KEY,
    tipoMovimiento ENUM('Entrada','Salida','Ajuste') NOT NULL,
    cantidad INT NOT NULL,
    fechaMovimiento DATE NOT NULL,
    observacion VARCHAR(255) NOT NULL,
    idLote INT NOT NULL,
    CONSTRAINT fk_movimiento_lote
        FOREIGN KEY (idLote)
        REFERENCES InventarioLote(idLote)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- Implementada con Exito (Cliente)
-- 1 user solo tiene un carrito por "compra"
-- el usuario sigue comprano (Activo),
-- el usuario pago el carrito (Finalizado)
-- el usuario abandono el carrito o lo cancelo antes de pagar (Abandonado) borra los datos del carrito en BD, lo "resetea"
CREATE TABLE IF NOT EXISTS Carrito (
    idCarrito INT AUTO_INCREMENT PRIMARY KEY,
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estadoCarrito ENUM('Activo','Finalizado','Abandonado') NOT NULL DEFAULT 'Activo',
    idUsuario INT NOT NULL,
    CONSTRAINT fk_carrito_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- Implementada con Exito (Cliente)
-- Un carrito puede tener varios productos
CREATE TABLE IF NOT EXISTS DetalleCarrito (
    idDetalleCarrito INT AUTO_INCREMENT PRIMARY KEY,
    cantidadCarrito INT NOT NULL, -- total de productos en carrito
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

--No implementada (Cliente) <no incluida> (NO es NECESARIA para la BD)
CREATE TABLE IF NOT EXISTS Descuento (
    idDescuento INT AUTO_INCREMENT PRIMARY KEY,
    tipoDescuento VARCHAR(100) NOT NULL,
    porcentaje DECIMAL(5,2) NOT NULL,
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    estadoDescuento ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
);

-- Implementada (De aqui sale el historial)
CREATE TABLE IF NOT EXISTS Pedido (
    idPedido INT AUTO_INCREMENT PRIMARY KEY,

    fechaPedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    fechaEntrega DATE NOT NULL,

     

    estadoPedido ENUM(
        'Pendiente',
        'En preparación',
        'Enviado',
        'Entregado',
        'Cancelado'
    ) NOT NULL DEFAULT 'Pendiente',

    tipoPedido ENUM('Individual','Empresa') NOT NULL,

    pedidoTotal DECIMAL(10,2) NOT NULL,

    observaciones VARCHAR(255),

    idUsuario INT NOT NULL,

    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

--NO implementada <no incluida> (NO es NECESARIA para la BD)
CREATE TABLE IF NOT EXISTS Pago (
    idPago INT AUTO_INCREMENT PRIMARY KEY,
    estadoPago ENUM('Pendiente','Pagado','Rechazado') NOT NULL DEFAULT 'Pendiente',
    fechaPago DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    idMetodoPago INT NOT NULL,
    idPedido INT NOT NULL UNIQUE,
    CONSTRAINT fk_pago_metodopago
        FOREIGN KEY (idMetodoPago)
        REFERENCES MetodoPagoUsuario(idMetodoPago)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_pago_pedido
        FOREIGN KEY (idPedido)
        REFERENCES Pedido(idPedido)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- Implementada con Exito
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

-- El usuario por defecto es admin@example.com
-- La contraseña por defecto es admin123
INSERT INTO users (nombre, email, password, rol) VALUES 
('Admin', 'admin@exam.ple', '$2y$10$ona2Mi3HzulIVmBW4Bki6uUhncN1NiM.ZjH4tmqH9K93..DVw9PnG', 'Administrador');

-- Insertar productos de la tienda (en el siguiente orden: Categoria, Presentacion y Producto)
-- Categoria para Cliente y Empresa
INSERT INTO Categoria (nombreCategoria) VALUES 
('Pejibaye');

INSERT INTO Categoria (nombreCategoria) VALUES 
('Harina');

-- Primer producto para Cliente
INSERT INTO Presentacion (tipoEmpaque, peso, tamaño) VALUES 
('Unidad', '340', 'Grande');

INSERT INTO Producto (nombreProducto, descripcion, precio, cantidadStock, ventaEmpresarial, idCategoria, idPresentacion) VALUES 
('Pejibaye Grande', 'Recién cosechados de nuestra finca, ideales para cocinar',
'2200', '24', FALSE, 1, 1);

-- Primer producto para Empresa
INSERT INTO Presentacion (tipoEmpaque, peso, tamaño) VALUES 
('Caja', '900', 'Lote Mediano');

INSERT INTO Producto (nombreProducto, descripcion, precio, cantidadStock, ventaEmpresarial, idCategoria, idPresentacion) VALUES 
('Lote Mediano de Pejibayes Grandes', 'Recién cosechados de nuestra finca, ideales para cocinar',
'7500', '5', TRUE, 1, 2);

-- Segundo producto para Cliente
INSERT INTO Presentacion (tipoEmpaque, peso, tamaño) VALUES 
('Bolsa', '50', 'Pequeño');

INSERT INTO Producto (nombreProducto, descripcion, precio, cantidadStock, ventaEmpresarial, idCategoria, idPresentacion) VALUES 
('Harina de Pejibaye', 'Llevolo a su mesa, comer saludable es comer feliz con nuestra harina',
'750', '15', FALSE, 2, 3);

SELECT * FROM users;

SELECT * FROM Categoria;

SELECT * FROM Presentacion;

SELECT * FROM Producto;

SELECT * FROM Empresa;

SELECT * FROM Pedido;

SELECT * FROM DetallePedido;