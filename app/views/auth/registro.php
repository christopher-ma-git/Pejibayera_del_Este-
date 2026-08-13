<?php require_once '../app/views/layouts/header.php'; ?>

<div class="login-body">
    <div class="registro-container">

        <h1>Pejibayera del Este</h1>
        <h2>Crear Cuenta</h2>

        <?php if(isset($data['error'])): ?>
            <div class="alert alert-danger"><?= $data['error'] ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/registro" method="POST">
            <label for="rol">Tipo de usuario</label>
            <select id="rol" name="rol" required>
                <option value="" selected disabled>
                    Seleccione un tipo de usuario
                </option>
                <option value="Cliente">
                    Cliente
                </option>
                <option value="Empresa">
                    Empresa
                </option>
            </select>

            <label for="nombre">Nombre de usuario</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ingrese un nombre de usuario">

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required placeholder="Ingrese su correo electrónico">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">

            <!-- Mostrar unicamente si el usuario selecciona Empresa en el select -->
            <div class="cedula-container">
                <label for="cedulaJuridica">Cédula Juridica</label>
                <input type="text" id="cedulaJuridica" name="cedulaJuridica" required placeholder="Ingrese su cédula juridica">
            </div>
            
            <div class="apellido-container">
                <label for="apellido"> Apellido (opcional)</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ingrese su apellido">
            </div>

            <label for="telefono">Teléfono (opcional)</label>
            <input type="tel" id="telefono" name="telefono" placeholder="Ingrese su número de teléfono">

            <label for="direccion">Dirección (opcional)</label>
            <input type="text" id="direccion" name="direccion" placeholder="Ingrese su dirección">

            <div class="btn-frmRgst-container">
                <button type="submit">
                    Confirmar
                </button>
            </div>
        </form>

        <p class="registro-texto">¿Ya tiene una cuenta?
            <a href="<?= BASE_URL ?>/auth/login">Iniciar sesión</a>
        </p>

    </div>
</div>

<script src="<?= BASE_URL ?>/js/script.js"></script>

<?php require_once '../app/views/layouts/footer.php'; ?>