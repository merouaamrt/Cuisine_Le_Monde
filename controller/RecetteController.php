<?php
require_once 'model/Recette.php';

class RecetteController {
    public function accueil() {
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $recettes = Recette::searchByTitre($_GET['search']);
        } else {
            $recettes = Recette::getAllWithUser(); // ✅ on récupère aussi le nom d'utilisateur
        }

        include 'view/accueil.php';
    }

    public function dashboard() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM recettes WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]); // ✅ seulement les recettes de l'utilisateur connecté
        $recettes = $stmt->fetchAll();
        require 'view/dashboard.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $ingredients = $_POST['ingredients'];
            $instructions = $_POST['instructions'];
            $duree = $_POST['duree'];
            $userId = $_SESSION['user_id'];

            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = 'img/';
                $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
                $imagePath = $uploadDir . $imageName;
                move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
            }

            $pdo = Database::connect();
            $stmt = $pdo->prepare("INSERT INTO recettes (titre, description, ingredients, instructions, duree, image_url, user_id) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $description, $ingredients, $instructions, $duree, $imagePath, $userId]);

            header("Location: index.php?page=dashboard");
        } else {
            include 'view/form_ajout.php';
        }
    }

    public function modifier($id) {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM recettes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $recette = $stmt->fetch();

        if (!$recette) {
            die("Accès non autorisé.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $ingredients = $_POST['ingredients'];
            $instructions = $_POST['instructions'];
            $duree = $_POST['duree'];
            $imageUrl = $recette['image_url'];

            if (!empty($_FILES['image']['name'])) {
                $imageFile = $_FILES['image'];
                $uploadDir = 'img/';
                $imageFileName = uniqid() . '_' . basename($imageFile['name']);
                $uploadFile = $uploadDir . $imageFileName;

                if (move_uploaded_file($imageFile['tmp_name'], $uploadFile)) {
                    $imageUrl = $uploadFile;
                }
            }

            $stmt = $pdo->prepare("UPDATE recettes SET titre = ?, description = ?, ingredients = ?, instructions = ?, duree = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$titre, $description, $ingredients, $instructions, $duree, $imageUrl, $id]);

            header("Location: index.php?page=dashboard");
        } else {
            include 'view/form_modif.php';
        }
    }

    public function supprimer($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM recettes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        header("Location: index.php?page=dashboard");
    }
}
?>

