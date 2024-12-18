<?php
require 'database.php';

if (!empty($_POST['uid'])) {
    $uid = $_POST['uid'];

    try {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO attendance (uid) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uid]);

        echo "UID successfully inserted!";
        Database::disconnect();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No UID provided.";
}
?>
