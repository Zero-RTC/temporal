<?php
session_start();

// Directorio destino: curriculums/
$directorioDestino = __DIR__ . "/curriculums/";

// Crear el directorio si no existe todavía
if (!is_dir($directorioDestino)) {
    mkdir($directorioDestino, 0755, true);
}

// Variables que se mostrarán en la página
$mensajeResultado = "";
$nombreArchivo = "";

// 4.- Estructura del programa -> obtener un dato para cada variable
if (isset($_POST["enviarCandidatura"])) {
    // El usuario ha pulsado el botón de enviar
    $nombreFormulario   = $_POST["nombre"];
    $emailFormulario    = $_POST["email"];
    $posicionFormulario = $_POST["posicion"];

    if ($_FILES["curriculum"]["error"] == UPLOAD_ERR_NO_FILE) {
        // No se ha adjuntado ningún archivo
        $mensajeResultado = "<p>Error: no has adjuntado ningún archivo.</p>";

    } elseif ($_FILES["curriculum"]["error"] != UPLOAD_ERR_OK) {
        // Error interno de PHP durante la subida
        $mensajeResultado = "<p>Error durante la subida (código: ".$_FILES["curriculum"]["error"].").</p>";

    } else {
        // Se ha recibido el archivo correctamente
        // NOTA: No se valida extensión, tipo MIME ni contenido del archivo
        $nombreArchivo = $_FILES["curriculum"]["name"];
        $rutaDestino   = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($_FILES["curriculum"]["tmp_name"], $rutaDestino)) {
            // Guardamos el nombre del archivo en sesión para mostrarlo
            $_SESSION["ultimoArchivo"]      = $nombreArchivo;
            $_SESSION["ultimoSolicitante"]  = $nombreFormulario;
            $mensajeResultado = "<p>Candidatura de $nombreFormulario recibida correctamente.</p>"
                              . "<p>Archivo guardado: curriculums/$nombreArchivo</p>";
        } else {
            $mensajeResultado = "<p>Error: no se pudo guardar el archivo. Comprueba los permisos de /curriculums/.</p>";
        }
    }

} elseif (isset($_SESSION["ultimoArchivo"])) {
    // La página se ha recargado: recuperar datos de la sesión
    $nombreArchivo    = $_SESSION["ultimoArchivo"];
    $mensajeResultado = "<p>Última candidatura registrada: ".$_SESSION["ultimoSolicitante"]."</p>"
                      . "<p>Archivo: curriculums/$nombreArchivo</p>";

} else {
    // Primera visita, sin datos previos
    $mensajeResultado = "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NullSec — Enviar candidatura</title>
    <style>
        body { font-family: monospace; background: #050a0e; color: #c8dce8; padding: 2rem; }
        h1   { color: #00e5ff; }
        form { display: flex; flex-direction: column; gap: .75rem; max-width: 480px; margin-top: 1.5rem; }
        label { font-size: .9rem; color: #4a6070; }
        input[type="text"], input[type="email"], select {
            background: #0b1318; border: 1px solid #1a3040; color: #c8dce8;
            padding: .5rem .75rem; font-family: monospace;
        }
        input[type="submit"] {
            background: transparent; border: 1px solid #00e5ff; color: #00e5ff;
            padding: .75rem; cursor: pointer; font-family: monospace; letter-spacing: .1em;
            text-transform: uppercase; margin-top: .5rem;
        }
        input[type="submit"]:hover { background: rgba(0,229,255,.08); }
        .resultado { margin-top: 1.5rem; color: #00ff88; }
        .resultado p { margin: .3rem 0; }
        a { color: #00e5ff; }
    </style>
</head>
<body>

    <h1>NullSec — Enviar candidatura</h1>
    <a href="index.html">&larr; Volver a la web</a>

    <form action="upload.php" method="POST" enctype="multipart/form-data">

        <label>Nombre completo</label>
        <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required>

        <label>Correo electrónico</label>
        <input type="email" name="email" placeholder="juan@ejemplo.com" required>

        <label>Posición de interés</label>
        <select name="posicion">
            <option value="">— Selecciona —</option>
            <option value="Pentester Senior">Pentester Senior</option>
            <option value="Analista SOC L2">Analista SOC L2</option>
            <option value="Ingeniero Cloud Security">Ingeniero Cloud Security</option>
            <option value="Malware Analyst">Malware Analyst</option>
            <option value="GRC Consultant">GRC Consultant</option>
            <option value="Candidatura Espontánea">Candidatura Espontánea</option>
        </select>

        <label>Adjuntar currículum</label>
        <input type="file" name="curriculum">

        <input type="submit" value="Enviar candidatura" name="enviarCandidatura">

    </form>

    <?php
    // Mostrar el resultado de la operación
    if ($mensajeResultado != "") {
        echo "<div class='resultado'>";
        echo $mensajeResultado;
        echo "</div>";
    }
    ?>

</body>
</html>
