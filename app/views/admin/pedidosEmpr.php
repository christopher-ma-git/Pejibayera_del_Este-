<?php require_once '../app/views/layouts/header.php'; ?>

<div class="dashboard">

    <!--MENÚ LATERAL-->
    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-seedling"></i>
            <span>Pejibayera del Este</span>
        </div>
            
        <nav class="menu">
            <a href="<?= BASE_URL ?>/admin/index" class="">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>

            <a href="<?= BASE_URL ?>/admin/pedidosCliente" class="">
                <i class="fa-regular fa-user"></i>
                Pedidos Clientes
            </a>

            <a href="<?= BASE_URL ?>/admin/pedidosEmpresa" class="active">
                <i class="fa-regular fa-building"></i>
                Pedidos Empresa
            </a>

            <a href="<?= BASE_URL ?>/auth/logout">
                <i class="fa-solid fa-door-closed"></i>
                Cerrar Sesión
            </a>
        </nav>
    </aside>

    <!--CONTENIDO-->
    <main class="contenido">
        <!--TÍTULO-->
        <section class="bienvenida">
            <h1>Pedidos de Empresas</h1>
            <p>
                Bienvenido nuevamente. Aquí puede visualizar el
                estado general de la empresa y administrar toda
                la plataforma.
            </p>
        </section>
        
        <!-- TABLA DE EMPRESAS -->
        <?php if (!empty($data['pedidos'])) : ?>
            <table class="tabla-pedidosEmpresa">
                <thead>
                    <tr>
                        <th>Nombre Usuario</th>
                        <th>Fecha Pedido</th>
                        <th>Fecha Entrega</th>
                        <th>Estado del Pedido</th>
                        <th>Precio</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($data['pedidos'] as $pedido): ?>
                        <tr>
                            <td> <?= htmlspecialchars($pedido['nombre']); ?> </td>
                            <td> <?= htmlspecialchars($pedido['fechaPedido']); ?> </td>
                            <td> <?= htmlspecialchars($pedido['fechaEntrega']); ?> </td>
                            <td>
                                <form action="<?= BASE_URL ?>/admin/updateEstado" method="POST">
                                    <input type="hidden" name="idPedido" value="<?= $pedido['idPedido']; ?>">
                                    <input type="hidden" name="origen" value="Empresa">

                                    <!-- 
                                        Se le puede dar una class a select o id a cada option
                                        para cambiar color e indicar en pantalla en que estado esta el pedido
                                    -->
                                    <select name="estadoPedido">
                                        <option value="">
                                            Seleccione una opción
                                        </option>
                                        <option value="Pendiente"
                                            <?= $pedido['estadoPedido'] == 'Pendiente' ? 'selected' : ''; ?>>
                                            Pendiente
                                        </option>
                                        <option value="En Preparación"
                                            <?= $pedido['estadoPedido'] == 'En Preparación' ? 'selected' : ''; ?>>
                                            En Preparación
                                        </option>
                                        <option value="Enviado"
                                            <?= $pedido['estadoPedido'] == 'Enviado' ? 'selected' : ''; ?>>
                                            Enviado
                                        </option>
                                        <option value="Entregado"
                                            <?= $pedido['estadoPedido'] == 'Entregado' ? 'selected' : ''; ?>>
                                            Entregado
                                        </option>
                                        <option value="Cancelado"
                                            <?= $pedido['estadoPedido'] == 'Cancelado' ? 'selected' : ''; ?>>
                                            Cancelado
                                        </option>
                                    </select>

                                    <button type="submit">
                                        Actualizar
                                    </button>
                                </form>
                            </td>
                            <td> ₡<?= number_format($pedido['subtotal'], 2); ?> </td>
                            <td> <?= htmlspecialchars($pedido['nombreProducto']); ?> </td>
                            <td> <?= htmlspecialchars($pedido['cantidad']); ?> </td>
                            <td> <?= htmlspecialchars($pedido['observaciones']); ?> </td>

                            <td>
                                <form action="<?= BASE_URL ?>/admin/deletePedido" method="POST">
                                    <input type="hidden" name="idPedido" value="<?= $pedido['idPedido']; ?>">
                                    <input type="hidden" name="origen" value="Empresa">
                                    <button type="submit">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="pedidos-vacio">
                <h3>Tabla de Pedidos Vacia</h3>

                <p>
                    Espera a que los usuarios hagan nuevos pedidos.
                </p>
            </div>
        <?php endif; ?>
    </main>    
</div>    