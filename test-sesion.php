<?php
session_start();
echo "<pre>";
echo "=== VERIFICACIÓN DE SESIÓN ===\n\n";
echo "validarSesion: " . (isset($_SESSION["validarSesion"]) ? $_SESSION["validarSesion"] : "NO EXISTE") . "\n";
echo "perfil: " . (isset($_SESSION["perfil"]) ? $_SESSION["perfil"] : "NO EXISTE") . "\n";
echo "usuario: " . (isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "NO EXISTE") . "\n";
echo "\nSesión completa:\n";
print_r($_SESSION);
echo "</pre>";
?>