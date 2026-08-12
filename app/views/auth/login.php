<?php require_once '../app/views/layouts/header.php'; ?>

<!-- <?php echo password_hash("jose", PASSWORD_DEFAULT); ?>  -->
<!-- Esto devuelve en pantalla "aquiVaSuContraseña" en formato hash
se ocupa para inicializar un admin, solo para usuarios insertados desde BD -->


<div class="login-body">
    <div class="login-container">

        <h1>Pejibayera del Este</h1>
        <h2>Iniciar Sesión</h2>

        <!-- De momento no funciona -->
        <?php if(isset($data['error'])): ?>
            <div class="alert alert-danger"><?= $data['error'] ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/login" method="POST">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" required placeholder="Ingrese su correo">

            <label for="password"> Contraseña </label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">

            <div class="btn-frmLogin-container">
                <button type="submit">Confirmar</button>
            </div>
        </form>

        <p class="registro-texto">
            ¿No tiene una cuenta?
            <a href="<?= BASE_URL ?>/auth/registro">Crear cuenta</a>
        </p>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>