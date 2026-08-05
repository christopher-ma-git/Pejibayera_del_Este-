<?php require_once '../app/views/layouts/header.php'; ?>
<!-- 
    Este bloque de código debe de ser mostrado como una pantalla emergente 
    Modificar para que el usuario pueda actualizar todos sus datos

    Esta pantalla es para Cliente y Empresa, por ende,
    apellido y cedula juridica deben de mostrarse segun el rol de usuario logueado
-->
<div class="login-body">
    <section class="registro-container">
        <h2>Actualizar Información</h2>

        <!-- Este botón debería de devolverse a Perfil sin hacer ningun cambio-->
        <button type="submit">
            <a href="<?= BASE_URL ?>/otherview/perfil" class="btn-frm-update">
                ← Volver
            </a>
        </button>

        <?php if(isset($data['error'])): ?>
            <div class="alert alert-danger"><?= $data['error'] ?></div>
        <?php endif; ?>
        <!--
            Debería traer los datos del usuario,
            mostrarlos en los input como placeholder y el mensaje actual dentro de ()
        -->
        <form id="frm-userUpdate" action="<?= BASE_URL ?>/user/update/" method="POST">
            <?php if ($_SESSION['user_role'] == 'Empresa'): ?>
                <label for="nombreUsuario">Nombre de Empresa</label>
                <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="<?= htmlspecialchars($data['usuario']['nombre']) ?> (Dejar en blanco para no actualizar)">

            <?php else: ?>
                <label for="nombreUsuario">Nombre de Usuario</label>
                <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="<?= htmlspecialchars($data['usuario']['nombre']) ?> (Dejar en blanco para no actualizar)">
            
            <?php endif; ?>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="<?= htmlspecialchars($data['usuario']['email']) ?> (Deje en blanco para no actualizar)">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="(Deje en blanco para no actualizar)">
            
            <?php if ($_SESSION['user_role'] == 'Empresa'): ?>
                <div class="cedulaJr-container">
                    <label for="cedulaJuridica">Cédula Juridica</label>
                    <input type="text" id="cedulaJuridica" name="cedulaJuridica" placeholder="<?= htmlspecialchars($data['empresa']['cedulaJuridica']) ?> (Deje en blanco para no actualizar)">
                </div>
            
            <?php else:  ?>
                <div class="apellido-container">
                    <label for="apellido"> Apellido (opcional)</label>
                    <input type="text" id="apellido" name="apellido" placeholder="<?= htmlspecialchars($data['usuario']['apellido']) ?> (Deje en blanco para no actualizar)">
                </div>
            <?php endif; ?>

            <label for="telefono">Teléfono (opcional)</label>
            <input type="tel" id="telefono" name="telefono" placeholder="<?= htmlspecialchars($data['usuario']['telefono']) ?> (Deje en blanco para no actualizar)">

            <label for="direccion">Dirección (opcional)</label>
            <input type="text" id="direccion" name="direccion" placeholder="<?= htmlspecialchars($data['usuario']['direccion']) ?> (Deje en blanco para no actualizar)">

            <div class="btn-frmUpdt-container">
                <!-- Este botón debería Actualizar los datos del usuario -->
                <button type="submit" class="btn-frm-update">
                    Actualizar Datos
                </button>
            </div>
        </form>

    </section>
</div>
<?php require_once '../app/views/layouts/footer.php'; ?>

