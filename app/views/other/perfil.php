<?php require_once '../app/views/layouts/header.php'; ?>

    <main class="main-perfil">
        <section class="usr-profile">
            <h2>Perfil</h2>
            <i class="fa-regular fa-user"></i>
            <!-- Aqui va consulta PHP, para sacar el nombre de usuario de BD, reemplazar h3 "Usuario"-->
            <h3><?= $_SESSION['user_name']; ?></h3>


            <!-- Este botón debería de cerrar sesión -->
            <div class="btn-logout-container">
                <button class="btn-logout" type="submit">
                    <a href="<?= BASE_URL ?>/auth/logout">
                        Cerrar Sesión
                    </a>
                </button>
            </div>

            <!-- Este botón debería de llevar a otra pantalla aparte (emergente, editPrf) -->
            <div class="prfUpdate-container">
                <button class="btn-prfUpdate" type="submit">
                    <a href="<?= BASE_URL ?>/otherview/editPrf">
                        Actualizar Datos
                    </a>
                </button>
            </div>
        </section>

        
        <!-- 
            Aqui va una consulta PHP (foreach) para sacar los pedidos y mostrarlos en una tabla
            La tabla debe tener: columnas (direccion,fechaDePedido,cantidad de productos,precioFinal) y filas(entregados, en curso y cancelados)
            Se debe cambiar el HTML!!!!
        -->
       <section class="historial-pedidos">
    <h2>Historial de Pedidos</h2>

    <?php if (empty($data['pedidos'])): ?>
        <p>No existen pedidos registrados.</p>
    <?php else: ?>
        <table class="tabla-pedidos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($data['pedidos'] as $pedido): ?>
                    <tr>
                        <td><?= $pedido['fechaPedido'] ?></td>
                        <td><?= $pedido['tipoPedido'] ?></td>
                        <td><?= $pedido['estadoPedido'] ?></td>
                        <td>₡<?= number_format($pedido['totalPedido'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
        </section>
    </main>

<?php require_once '../app/views/layouts/footer.php'; ?>

