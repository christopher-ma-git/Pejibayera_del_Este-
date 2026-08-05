<?php require_once '../app/views/layouts/header.php'; ?>

   <main>
        <section class="temporada">
            <div class="titulo-temp">
                <h2>Calendario de Temporada</h2>
                <p>
                    La temporada principal del pejibaye en Costa Rica se extiende
                    desde abril hasta setiembre. Los meses de mayor producción son
                    mayo y julio.
                </p>
            </div>

            <div class="calendario">

                <div class="mes-fuera">Enero</div>
                <div class="mes-fuera">Febrero</div>
                <div class="mes-fuera">Marzo</div>
                <div class="mes-temporada">Abril</div>
                <div class="mes-pico">Mayo</div>
                <div class="mes-temporada">Junio</div>

                <div class="mes-pico">Julio</div>
                <div class="mes-temporada">Agosto</div>
                <div class="mes-temporada">Setiembre</div>
                <div class="mes-fuera">Octubre</div>
                <div class="mes-fuera">Noviembre</div>
                <div class="mes-fuera">Diciembre</div>

            </div>

            <div class="leyenda">
                <span>Fuera de temporada: Rojo |</span>
                <span>Temporada: Verde |</span>
                <span>Pico de producción: Amarillo</span>
            </div>
        </section>
    </main>

<?php require_once '../app/views/layouts/footer.php'; ?>