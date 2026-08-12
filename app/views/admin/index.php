<?php require_once '../app/views/layouts/header.php'; ?>

<div class="dashboard">

    <!--MENÚ LATERAL-->
    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-seedling"></i>
            <span>Pejibayera del Este</span>
        </div>
            
        <nav class="menu">
            <a href="<?= BASE_URL ?>/admin/index" class="active">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>

            <a href="<?= BASE_URL ?>/admin/pedidosCliente" class="">
                <i class="fa-regular fa-user"></i>
                Pedidos Clientes
            </a>

            <a href="<?= BASE_URL ?>/admin/pedidosEmpresa" class="">
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
            <h1>Dashboard</h1>
            <p>
                Bienvenido nuevamente. Aquí puede visualizar el
                estado general de la empresa y administrar toda
                la plataforma.
            </p>
        </section>

        <!--TARJETAS-->
        <section class="estadisticas">
            <article class="estadistica">
                <h4>Total Clientes</h4>
                <h2><?= $data['clientes']['total']; ?></h2>
                <span>
                    +<?= number_format($data['clientes']['crecimiento'], 2); ?>%
                    este mes
                </span>
            </article>

            <article class="estadistica">
                <h4>Total Empresas</h4>
                <h2><?= $data['empresas']['total']; ?></h2>
                <span>
                    +<?= number_format($data['empresas']['crecimiento'], 2); ?>%
                    este mes
                </span>
            </article>

            <article class="estadistica">
                <h4>Pedidos</h4>
                <h2><?= $data['pedidos']['total']; ?></h2>
                <span>
                    +<?= number_format($data['pedidos']['crecimiento'], 2); ?>%
                    este mes
                </span>
            </article>

            <article class="estadistica">
                <h4>Ventas</h4>
                <h2>₡<?= number_format($data['ventas']['total'], 2); ?></h2>
                <span>
                    +<?= number_format($data['ventas']['crecimiento'], 2); ?>%
                    este mes
                </span>
            </article>
        </section>

        <!--PANEL PRINCIPAL-->
        <section class="panel-dashboard">
            <!-- Productos -->
            <article class="card">
                <div class="titulo-card">
                    <h2>Productos Más Vendidos</h2>
                </div>
                <ul class="productos">
                    <?php foreach ($data['productosMasVendidos'] as $producto): ?>
                        <li>
                            <span>
                                <?= htmlspecialchars($producto['nombreProducto']); ?>
                            </span>
                            <strong>
                                <?= $producto['totalVentas']; ?> ventas
                            </strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>

        </section>
    </main>    
</div>