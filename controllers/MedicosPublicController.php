<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../config/db.php';
require_once '../models/Medico.php';
require_once '../models/Especialidad.php';

$medico       = new Medico($pdo);
$especialidad = new Especialidad($pdo);

// --- Lógica de Filtros ---
$busqueda = filter_var($_GET['busqueda'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
$especialidadFiltro = filter_var($_GET['especialidad'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

$filtros = [
    'busqueda' => $busqueda,
    'especialidad' => $especialidadFiltro
];

// Se obtienen los médicos ya filtrados desde el modelo
$listadoMedicos = $medico->getAll($filtros);

// Se obtienen todas las especialidades para poblar el dropdown de filtros
$listadoEspecialidad = $especialidad->getAll();
?>
