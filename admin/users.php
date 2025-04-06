<?php
require_once '../config/database.php';
require_once '../objects/user.php';

session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Gestion des actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if($action === 'toggle_status' && $id > 0) {
    $user->id = $id;
    if($user->readOne()) {
        $user->is_active = !$user->is_active;
        $user->updateStatus();
    }
    header('Location: users.php');
    exit;
}

$users = $user->readAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <?php include 'partials/nav.php'; ?>
        
        <div class="flex-1">
            <?php include 'partials/header.php'; ?>
            
            <main class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Gestion des Utilisateurs</h2>
                    <a href="user_edit.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i> Ajouter un utilisateur
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= ucfirst($row['role']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['department_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $row['is_active'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="user_edit.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="users.php?action=toggle_status&id=<?= $row['id'] ?>" class="text-<?= $row['is_active'] ? 'red' : 'green' ?>-500 hover:text-<?= $row['is_active'] ? 'red' : 'green' ?>-700">
                                        <i class="fas fa-<?= $row['is_active'] ? 'times' : 'check' ?>"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>