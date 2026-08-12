<?php require_once '../app/views/layouts/header.php'; ?>

<main class="main-carrito">
    <section class="carrito-container">

        <div class="carrito-header">
            <h2>Carrito de Compras</h2>
        </div>

        <div class="btnVolver-carrito">
            <button class="btn-volver">
                <a href="<?= $inicioUrl ?>">
                    <i class="fa-solid fa-left-long"></i>
                </a>
            </button>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($data['productos'])) : ?>
            <table class="tabla-carrito">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Presentación</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($data['productos'] as $producto) : ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($producto['nombreProducto']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($producto['descripcion']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($producto['nombreCategoria']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($producto['tipoEmpaque']) ?>
                                -
                                <?= htmlspecialchars($producto['tamaño']) ?>
                                (<?= htmlspecialchars($producto['peso']) ?> g)
                            </td>

                            <td>
                                <form action="<?= BASE_URL ?>/carrito/updateCantidad" method="POST">
                                    <input type="hidden" name="idDetalleCarrito" 
                                            value="<?= $producto['idDetalleCarrito']; ?>">

                                    <input type="number" name="cantidad" min="1" max="<?= $producto['cantidadStock']; ?>"
                                            value="<?= $producto['cantidadCarrito']; ?>">
                                    <button type="submit">
                                        Actualizar
                                    </button>
                                </form>
                            </td>

                            <td>
                                ₡<?= number_format($producto['precio'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <button>
                                    <a href="<?= BASE_URL ?>/carrito/delete/<?= $producto['idDetalleCarrito']; ?>">
                                        Eliminar
                                    </a>
                                </button>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="resumen-carrito">
                <h3>
                    Total Productos:
                    <?= $data['cantidadTotal']; ?>
                </h3>

                <h3>
                    Total:
                    ₡<?= number_format($data['total'], 0, ',', '.'); ?>
                </h3>

                <div class="btn-ResumenCarrito-container">
                    <button>
                        <a href="<?= BASE_URL ?>/carrito/eliminarCarrito" class="btn-frmCarrito-eliminarTodo">
                            Eliminar Carrito
                        </a>
                    </button>

                    <button>
                        <a href="<?= BASE_URL ?>/carrito/comprar" class="btn-frmCarrito-comprarTodo">
                            Comprar Ahora
                        </a>
                    </button>
                </div>
            </div>
            
        <?php else : ?>
            <div class="carrito-vacio">
                <h3>Tu carrito está vacío.</h3>

                <p>
                    Agrega productos para comenzar una compra.
                </p>

                <div class="btn-salir-container">
                    <button>
                        <a href="<?= $inicioUrl ?>">
                            Volver a la tienda
                        </a>
                    </button>
                </div>
            </div>
        <?php endif; ?>

    </section>
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>