<?php
    $inicioUrl = BASE_URL . '/otherview/index/';
    if (isset($_SESSION['user_role'])) {
        switch ($_SESSION['user_role']) {
            case 'Administraor':
                $inicioUrl = BASE_URL . '/admin/index';
                break;
            case 'Empresa':
                $inicioUrl = BASE_URL . '/empresa/index';
                break;
            default:
                $inicioUrl = BASE_URL . '/user/index';
                break;
        }
    }

    $mostrarHeader = true;
    if (isset($_SESSION['user_role'])) {
        switch ($_SESSION['user_role']) {
            case 'Administrador':
                $mostrarHeader = false;
                break;
            default:
            $mostrarHeader = true;
            break;
        }
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pejibayera Del Este</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/inicio.css"> <!-- Antes /css/style.css -->

</head>
<body class="body-container">

    <?php if ($mostrarHeader): ?>
    <header class="topbar">

        <div class="logo">
            <i class="fa-solid fa-seedling"></i>
            <span>Pejibayera del Este</span>
        </div>

        <nav>
            <a href="<?= $inicioUrl ?>" class="active">Inicio</a>
            <a href="<?= BASE_URL ?>/otherview/contacto">Contacto</a>
            <a href="<?= BASE_URL ?>/otherview/temporada">Temporadas</a>
        </nav>

        <div class="actions">
            <div class="carrito">
                <a href="<?= BASE_URL ?>/carrito/index">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
                <span class="cart-count">
                    <?= $data['cantidadTotal'] ?? 0; ?>
                </span>
            </div>

            <div class="usr">
                <a href="<?= BASE_URL ?>/otherview/perfil">
                    <i class="fa-regular fa-user"></i>
                </a>
            </div>
        </div>

    </header>
    <?php endif; ?>

