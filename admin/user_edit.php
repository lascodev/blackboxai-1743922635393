<?php
require_once '../config/database.php';
require_once '../objects/user.php';
require_once '../objects/department.php';

session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$department = new Department($db);

$id = $_GET['id'] ?? 0;
if($id > 0) {
    $user->id = $id;
    if(!$user->readOne()) {
        header('Location: users.php');
        exit;
    }
}

$departments = $department->read();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->username = $_POST['username'];
    $user->role = $_POST['role'];
    $user->department_id = $_POST['department_id'];
    
    if(!empty($_POST['password'])) {
        $user->password = $_POST['password'];
    }
    
    if($id > 0) {
        // Mise à jour
        if(!empty($_POST['password'])) {
            $user->update();
        } else {
            $user->updateWithoutPassword();
        }
    } else {
        // Création
        $user->create();
    }
    
    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> Utilisateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <?php include 'partials/nav.php'; ?>
        
        <div class="flex-1">
            <?php include 'partials/header.php'; ?>
            
            <main class="p-6">
                <h2 class="text-2xl font-bold mb-6"><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> un utilisateur</h2>
                
                <form method="POST" class="bg-white rounded-lg shadow p-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user->username ?? '') ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" <?= $id === 0 ? 'required' : '' ?>
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            placeholder="<?= $id > 0 ? 'Laisser vide pour ne pas modifier' : '' ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="role">Rôle</label>
                        <select id="role" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="admin" <?= ($user->role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="focal" <?= ($user->role ?? '') === 'focal' ? 'selected' : '' ?>>Focal</option>
                            <option value="agent" <?= ($user->role ?? '') === 'agent' ? 'selected' : '' ?>>Agent</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="department_id">Département</label>
                        <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">-- Sélectionner --</option>
                            <?php while($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($user->department_id ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <a href="users.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
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