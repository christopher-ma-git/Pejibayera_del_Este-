<?php require_once '../app/views/layouts/header.php'; ?>

    <main>
        <section class="hero-section">
            <div class="hero-text">
                <h1>¡Bienvenido a Pejibayera del Este!</h1>
                <p>Encuentra los mejores pejibayes frescos directamente del productor.</p>
                <button type="button">Comprar ahora</button>
            </div>

            <div class="hero-image">
                 <i class="fa-solid fa-seedling"></i>
            </div>
        </section>

        <section class="cards-section">
            <h2>Productos Destacados</h2>
            <div class="cards">
                <?php if (!empty($data['productos'])) : ?>
                <?php foreach ($data['productos'] as $producto) : ?>

                <article class="card">
                    <h3><?php echo htmlspecialchars($producto['nombreProducto']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>

                    <span class="precio">
                        ₡<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
                    </span>

                    <?php if (isset($_SESSION['user_id'])) : ?>
                    <button type="button">
                        <a href="<?= BASE_URL ?>/carrito/add/<?= $producto['idProducto']; ?>">
                            Agregar al carrito
                        </a>
                    </button>

                    <?php else : ?>
                    <button type="button">
                        <a href="<?= BASE_URL ?>/auth/login">
                            Agregar al carrito
                        </a>
                    </button>

                    <?php endif; ?>
                    
                </article>
                <?php endforeach; ?>
                
                <?php else : ?>
                    <p>No hay productos disponibles.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="banner-promo">
            <div>
                <!-- Esta parte debería tener consulta PHP (foreach) saca los productos con descuento de la BD (porcentaje, producto, tiempo) -->
                <h2>¡Oferta de Temporada!</h2>
                <p>Obtén un 25% de descuento en todos los pejibayes frescos durante esta semana.</p>
            </div>
            <button  class="btn-promo" type="button"> <a href="">Aprovechar oferta</a></button> 
            <!-- Verificar si el usuario esta logedo, si lo esta, añadir producto con descuento al carrito, si no, mandarlo al login-->
        </section>
    </main>

<?php require_once '../app/views/layouts/footer.php'; ?>