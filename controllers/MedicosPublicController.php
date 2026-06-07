<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../config/db.php';
require_once '../models/Medico.php';
require_once '../models/Especialidad.php';

$medico       = new Medico($pdo);
$especialidad = new Especialidad($pdo);

$listadoMedicos      = $medico->getAll();
$listadoEspecialidad = $especialidad->getAll();

$especialidadFiltro = $_GET['especialidad'] ?? null;

if ($especialidadFiltro) {
    $listadoMedicos = array_filter($listadoMedicos, function ($m) use ($especialidadFiltro, $listadoEspecialidad) {
        $nombreEsp = '';
        foreach ($listadoEspecialidad as $e) {
            if ($e['id_especialidad'] == $especialidadFiltro) {
                $nombreEsp = $e['nombre'];
                break;
            }
        }
        return str_contains($m['especialidades'] ?? '', $nombreEsp);
    });
}
?>
