<?php
require_once 'Database.php';

class Recette {
    public static function getAllWithUser() {
        $pdo = Database::connect();
        $stmt = $pdo->query("
            SELECT recettes.*, users.username 
            FROM recettes 
            JOIN users ON recettes.user_id = users.id
            ORDER BY recettes.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    public static function searchByTitre($mot) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT recettes.*, users.username 
            FROM recettes 
            JOIN users ON recettes.user_id = users.id 
            WHERE recettes.titre LIKE :mot
            ORDER BY recettes.created_at DESC
        ");
        $stmt->execute(['mot' => '%' . $mot . '%']);
        return $stmt->fetchAll();
    }
    
}
?>
