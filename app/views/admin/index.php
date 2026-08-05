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
                <h2>2,847</h2>
                <span>+12% este mes</span>
            </article>

            <article class="estadistica">
                <h4>Total Empresas</h4>
                <h2>184</h2>
                <span>+8% este mes</span>
            </article>

            <article class="estadistica">
                <h4>Pedidos</h4>
                <h2>5,621</h2>
                <span>+17% este mes</span>
            </article>

            <article class="estadistica">
                <h4>Ventas</h4>
                <h2>₡128,430</h2>
                <span>+22% este mes</span>
            </article>
        </section>

        <!--PANEL PRINCIPAL-->
        <section class="panel-dashboard">
            <!-- Ventas -->
            <article class="card-grande">
                <div class="titulo-card">
                    <h2>Ventas Mensuales</h2>
                    <span>Enero - Junio 2026</span>
                </div>
                <div class="grafico">
                    <div class="linea"></div>
                    <p>📈 Aquí irá el gráfico de ventas.</p>
                </div>
            </article>

            <!-- Productos -->
            <article class="card">
                <div class="titulo-card">
                    <h2>Productos Más Vendidos</h2>
                </div>
                <ul class="productos">
                    <li>
                        <span>🥇 Pejibaye Grande</span>
                        <strong>425 ventas</strong>
                    </li>
                    <li>
                        <span>🥈 Pejibaye Pequeño</span>
                        <strong>382 ventas</strong>
                    </li>
                    <li>
                        <span>🥉 Harina de Pejibaye</span>
                        <strong>201 ventas</strong>
                    </li>
                    <li>
                        <span>🌴 Pejibaye Cocido</span>
                        <strong>148 ventas</strong>
                    </li>
                </ul>
            </article>

        </section>
    </main>    
</div>
