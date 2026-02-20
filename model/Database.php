<?php
class Database {
    public static function connect() {
        try {
            // Connexion à la base de données en utilisant PDO
            $pdo = new PDO(
                'mysql:host=mysql03.univ-lyon2.fr;dbname=php_meamirat;charset=utf8mb4',
                'php_meamirat',
                'MFwbnqdIEtI_II6LGvxjCwDD1' // Remplace bien sûr par ton vrai mot de passe
            );
            // Configuration des attributs de PDO pour mieux gérer les erreurs
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;  // Retourne l'objet PDO pour une utilisation ultérieure
        } catch (PDOException $e) {
            // En cas d'échec, on arrête l'exécution du script avec le message d'erreur
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
}
?>
