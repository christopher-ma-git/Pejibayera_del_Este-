<?php require_once '../app/views/layouts/header.php'; ?>
    <main class="main-compra">

    <div class="FacturaCompra-container">
            <section class="factura-container">
                <div class="factura-header">
                    <h2>Factura de Compra</h2>
                </div>
            
                <div class="btnVolver-carrito">
                    <button class="btn-volver">
                        <a href="<?= BASE_URL ?>/carrito/index">
                            <i class="fa-solid fa-left-long"></i>
                        </a>
                    </button>
                </div>
            
                <table class="tabla-factura">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                
                    <tbody>
                        <?php foreach ($data['productos'] as $producto): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($producto['nombreProducto']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($producto['cantidadCarrito']); ?>
                                </td>

                                <td>
                                    ₡<?= number_format( $producto['precio'], 2); ?>
                                </td>

                                <td>
                                    ₡<?= number_format( $producto['cantidadCarrito'] * $producto['precio'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            
                <div class="factura-total">
                    <p>
                        <strong>Fecha:</strong>
                        <?= $data['fecha']; ?>
                    </p>
                        
                    <p>
                        <strong>Total:</strong>
                        ₡<?= number_format($data['total'], 2); ?>
                    </p>
                </div>
            </section>

            <section class="pago-container">
                <h2>Datos del Pago</h2>
            
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            
                <form action="<?= BASE_URL ?>/pedido/store" method="POST">
                    <label for="numeroTarjeta">Número de Tarjeta</label>
                    <input type="text" id="numeroTarjeta" name="numeroTarjeta" 
                        maxlength="16" minlength="16" pattern="[0-9]{16}"
                        required placeholder="**** **** **** 1234">
                
                    <label for="fechaVencimiento">Fecha de Vencimiento</label>
                    <input type="date" id="fechaVencimiento" name="fechaVencimiento" required>
                
                    <label for="pin"> PIN</label>
                    <input type="password" id="pin" name="pin"
                        maxlength="4" minlength="4" pattern="[0-9]{4}"
                        required placeholder="****">
                
                    <div class="btn-frmPago-container">
                        <button type="submit">
                            Confirmar Compra
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <?php require_once '../app/views/layouts/footer.php'; ?>