<?php
declare(strict_types=1);
require_once __DIR__ . '/../BD/conexion.php';
require_once __DIR__ . '/../funciones/funciones.php';
if (session_status() === PHP_SESSION_NONE) { session_name('SHIZEN_CLIENTE_SESSION'); session_start(); }
if (empty($_SESSION['id_usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    http_response_code(403); exit('Solicitud no válida.');
}
$nombre = limpiarTexto($_POST['nombre'] ?? '');
$apellido = limpiarTexto($_POST['apellido'] ?? '');
$direccion = limpiarTexto($_POST['direccion'] ?? '');
$localidad = limpiarTexto($_POST['localidad'] ?? '');
if ($nombre === '' || $apellido === '') { http_response_code(422); exit('Nombre y apellido son obligatorios.'); }
$stmt = obtenerConexion()->prepare('UPDATE usuario SET nombre=?, apellido=?, direccion=?, localidad=? WHERE id_usuario=?');
$stmt->execute([$nombre, $apellido, $direccion ?: null, $localidad ?: null, (int)$_SESSION['id_usuario']]);
$_SESSION['usuario_nombre'] = $nombre;
$_SESSION['usuario_apellido'] = $apellido;
$_SESSION['usuario_direccion'] = $direccion;
$_SESSION['usuario_localidad'] = $localidad;
header('Location: ../index.php'); exit;
