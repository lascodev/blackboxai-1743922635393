<?php
require_once '../config/database.php';
require_once '../objects/department.php';

session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$department = new Department($db);

$id = $_GET['id'] ?? 0;
if($id > 0) {
    $department->id = $id;
    if(!$department->readOne()) {
        header('Location: departments.php');
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department->name = $_POST['name'];
    
    if($id > 0) {
        $department->update();
    } else {
        $department->create();
    }
    
    header('Location: departments.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> Département</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <?php include 'partials/nav.php'; ?>
        
        <div class="flex-1">
            <?php include 'partials/header.php'; ?>
            
            <main class="p-6">
                <h2 class="text-2xl font-bold mb-6"><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> un département</h2>
                
                <form method="POST" class="bg-white rounded-lg shadow p-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="name">Nom du département</label>
                        <input type="text" id="name" name="name" 
                               value="<?= htmlspecialchars($department->name ?? '') ?>" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <a href="departments.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Annuler
                        </a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>