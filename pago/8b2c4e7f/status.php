<?php
header('Content-Type: application/json');
include __DIR__ . '/../../db.php';

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT estado FROM clientes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['error' => 'No encontrado']);
    } else {
        echo json_encode(['estado' => $row['estado']]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error interno']);
}
?>
