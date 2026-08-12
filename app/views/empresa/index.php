<?php require_once '../app/views/layouts/header.php'; ?>

<!-- 
    Esta pantalla debe de mostrar el contenido para usuarios con rol "Empresa".
    Cargar información desde Base de Datos (BD) y mostrarla mediante consultas PHP.
    Validar inputs con PHP y etiquetas HTML auxiliares.
    Debera seguir un flujo similar al de usuarios con rol "Cliente" es decir:
        1. Se loguea o registra
        2. Entra a su pagina de inicio/home (app/views/empresa/index)
        3. Agrega un producto al Carrito
        4. Actualiza la cantidad, Elimina 1 producto, Elimina todo el Carrito o lo Compra
        5. En caso de "Comprar", se redirige a la pantalla de compra donde se muestra la "Factura" y el formulario para ingresar sus datos de pago
        6. Cuando le da a "Confirmar compra", se vacia el Carrito, se reduce la cantidad de Stock del producto y se agrega el Pedido
        7. Si el usuario "Empresa" se dirige a su perfil (/other/perfil) se muestra el "Historial de Pedido"

-->

<!-- CONTENIDO-->
<main>
    <!-- Bienvenida -->
    <section class="hero-section">
        <div class="hero-text">
            <h1>Bienvenido, Empresa</h1>
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
            <!--TARJETA 1-->
            <article class="card pedido">
                <h2>
                    <i class="fa-solid fa-cart-plus"></i>
                    Nuevo Pedido Mayorista
                </h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/carrito/addEmpresa" method="POST">
                    <div class="campo">
                        <label for="producto">
                            Producto
                        </label>

                        <!-- 
                            Este select debe tener una consulta PHP (foreach) para mostrar los Productos con ventaEmpresarial = TRUE
                            1. Debe cargar el nombre del producto, su precio y cantidad en Stock ej: Lote Mediano de Pejibayes Grandes - ₡7500 (disponible: 5und)
                            2. Validar que sí o sí escoje un producto (validar en PHP)
                        -->
                        <select id="producto" name="idProducto" required>
                            <option value="">
                                Seleccione un producto
                            </option>

                            <?php foreach($data['productos'] as $producto): ?>
                                <option value="<?= $producto['idProducto']; ?>">
                                    <?= htmlspecialchars($producto['nombreProducto']);?>
                                    -
                                    ₡<?= number_format($producto['precio'], 2)?>
                                    (Disponible: <?= $producto['cantidadStock']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="campo">
                        <!-- Validar que escoje más de 10 y menos de 50 cajas/unidades (validar en PHP) -->
                        <label for="cantidad">
                            Cantidad de cajas
                        </label>
                        <input type="number" id="cantidad" name="cantidad" min="10" max="50" required placeholder="Ejemplo: 30">
                    </div>

                    <!-- Validar que escoje una fecha de entrega mayor a la actual y que no sea Sábado o Domingo (validar en PHP) -->
                    <div class="campo">
                        <label for="fechaEntrega">
                            Fecha de entrega
                        </label>
                        <input type="date" id="fechaEntrega" name="fechaEntrega" required>
                    </div>

                    <!-- Este campo no es requerido que sea llenado -->
                    <div class="campo">
                        <label for="observaciones">
                            Observaciones
                        </label>
                        <textarea id="observaciones" name="observaciones" rows="4" placeholder="Escriba alguna observación..."></textarea>
                    </div>

                    <!-- El botón debe agregar el producto al carrito -->
                    <button type="submit" class="btn-principal">
                        Agregar Pedido
                    </button>
                </form>
            </article>

            <!--TARJETA 2-->
            <!-- 
                Información estática, muestra las reglas de negocio para empresa
                1. El pedido debe ser minimo de 10 cajas/unidades y maximo 50(validar en PHP y HTML)
                2. Horario de Lunes a Viernes, no pueden realizar pedidos con "Fecha de Entrega" para Sábado o Domingo (validar en PHP)
                3. Actualmente solo se simula el pago con tarjeta
            -->
            <article class="card informacion">
                <h2>
                    <i class="fa-solid fa-circle-info"></i>
                    Información Comercial
                </h2>

                <div class="info-comercial">
                    <div class="dato">
                        <h3>
                            Pedido mínimo
                        </h3>

                        <p>
                            10 cajas por pedido.
                        </p>
                    </div>

                    <div class="dato">
                        <h3>
                            Horario de atención
                        </h3>

                        <p>
                            Lunes a Viernes
                            <br>
                            7:00 a.m. - 5:00 p.m.
                        </p>
                    </div>

                    <div class="dato">
                        <h3>
                            Zona de entrega
                        </h3>
                        
                        <p>
                            Cartago, San José,
                            Heredia y Alajuela.
                        </p>
                    </div>

                    <div class="dato">
                        <h3>
                            Formas de pago
                        </h3>

                        <p>
                            SINPE Móvil,
                            Transferencia Bancaria
                            y Efectivo.
                        </p>
                    </div>

                </div>
            </article>
        </div>
    </section>
</main>

<?php require_once '../app/views/layouts/footer.php'; ?>