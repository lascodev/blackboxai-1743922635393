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

// Gestion des actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if($action === 'delete' && $id > 0) {
    $department->id = $id;
    $department->delete();
    header('Location: departments.php');
    exit;
}

$departments = $department->read();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Départements</title>
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
                    <h2 class="text-2xl font-bold">Gestion des Départements</h2>
                    <a href="department_edit.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i> Ajouter un département
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($row = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="department_edit.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="departments.php?action=delete&id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Supprimer ce département ?')">
                                        <i class="fas fa-trash"></i>
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