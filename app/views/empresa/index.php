<?php require_once '../app/views/layouts/header.php'; ?>

<main>

    <!-- Bienvenida -->
    <section class="hero-section">

        <div class="hero-text">

            <h1>
                Bienvenido, Empresa
            </h1>

            <p>
                Administre sus pedidos mayoristas de manera rápida y sencilla.
                Desde este panel podrá solicitar productos, revisar pedidos
                realizados y comunicarse directamente con la Pejibayera del Este.
            </p>

        </div>

    </section>

    <!-- Tarjetas -->
    <section class="cards-section">

        <div class="cards">

            <!-- NUEVO PEDIDO -->
            <article class="card pedido">

                <h2>

                    <i class="fa-solid fa-cart-plus"></i>

                    Nuevo Pedido Mayorista

                </h2>

                <form action="<?= BASE_URL ?>/empresa/crearPedido" method="POST">

                    <div class="campo">

                        <label for="idProducto">Producto</label>

                        <select
                            name="idProducto"
                            id="idProducto"
                            required>

                            <option value="">
                                Seleccione un producto
                            </option>

                            <?php foreach ($data['productos'] as $producto): ?>

                                <option value="<?= $producto['idProducto']; ?>">

                                    <?= htmlspecialchars($producto['nombreProducto']); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="campo">

                        <label for="cantidad">

                            Cantidad de cajas

                        </label>

                        <input
                            type="number"
                            id="cantidad"
                            name="cantidad"
                            min="1"
                            required>

                    </div>

                    <div class="campo">

                        <label for="fechaEntrega">

                            Fecha de entrega

                        </label>

                        <input
                            type="date"
                            id="fechaEntrega"
                            name="fechaEntrega"
                            required>

                    </div>

                    <div class="campo">

                        <label for="comentario">

                            Observaciones

                        </label>

                        <textarea
                            id="comentario"
                            name="comentario"
                            rows="4"
                            placeholder="Escriba alguna observación..."></textarea>

                    </div>

                    <button
                        type="submit"
                        class="btn-principal">

                        Solicitar Pedido

                    </button>

                </form>

            </article>

            <!-- MIS PEDIDOS -->
            <article class="card pedidos">

                <h2>

                    <i class="fa-solid fa-box"></i>

                    Mis Pedidos

                </h2>

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Total</th>

                            <th>Estado</th>

                            <th>Fecha Pedido</th>

                            <th>Entrega</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($data['pedidos'])): ?>

                            <?php foreach ($data['pedidos'] as $pedido): ?>

                                <tr>

                                    <td>

                                        <?= $pedido['idPedido']; ?>

                                    </td>

                                    <td>

                                        ₡<?= number_format($pedido['pedidoTotal'], 2); ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars($pedido['estadoPedido']); ?>

                                    </td>

                                    <td>

                                        <?= $pedido['fechaPedido']; ?>

                                    </td>

                                    <td>

                                        <?= $pedido['fechaEntrega']; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="5">

                                    No hay pedidos registrados.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </article>

            <!-- INFORMACIÓN -->
            <article class="card informacion">

                <h2>

                    <i class="fa-solid fa-circle-info"></i>

                    Información Comercial

                </h2>

                <p>

                    <strong>Pedido mínimo:</strong>
                    10 cajas.

                </p>

                <p>

                    <strong>Horario:</strong>
                    Lunes a Viernes
                    7:00 a.m. - 5:00 p.m.

                </p>

                <p>

                    <strong>Formas de pago:</strong>
                    SINPE Móvil,
                    Transferencia Bancaria
                    y Efectivo.

                </p>

            </article>

            <!-- CONTACTO -->
            <article class="card contacto">

                <h2>

                    <i class="fa-solid fa-phone"></i>

                    Contacto

                </h2>

                <p>

                    <strong>Teléfono:</strong>

                    +506 8888-8888

                </p>

                <p>

                    <strong>Correo:</strong>

                    ventas@pejibayera.cr

                </p>

                <button
                    type="button"
                    class="btn-principal">

                    <i class="fa-brands fa-whatsapp"></i>

                    Contactar

                </button>

            </article>

        </div>

    </section>

</main>

<?php require_once '../app/views/layouts/footer.php'; ?>