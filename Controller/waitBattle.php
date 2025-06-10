<?php
require_once '../Controller/connection.php';
session_start();

if (!isset($_SESSION['roomCode'])) {
    echo json_encode(['ready' => false, 'error' => 'Room code not set']);
    exit();
}

$roomCode = $_SESSION['roomCode'];
$_SESSION['battle']['ready'] = false;

try {
    $isReadyStmt = $conn->prepare("SELECT * FROM room WHERE rooCode = ?");
    $isReadyStmt->execute([$roomCode]);
    $isReady = $isReadyStmt->fetch(PDO::FETCH_ASSOC);

    if ($isReady['rooIsReadyPlayer2'] != false || $isReady['rooIsReadyPlayer2'] != null) {
        $_SESSION['battle']['ready'] = true;
        $_SESSION['battle']['player1'] = $isReady['rooPlaCode1'];
        $_SESSION['battle']['player2'] = $isReady['rooPlaCode2'];
        echo json_encode(['ready' => true]);
        
    } else {
        $_SESSION['battle']['ready'] = false;
        echo json_encode(['ready' => false]);
    }
} catch (PDOException $e) {
    error_log('Error checking readiness: ' . $e->getMessage());
    echo json_encode(['ready' => false, 'error' => 'Database error']);
}

exit();
?>