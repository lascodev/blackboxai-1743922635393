<?php
require_once '../config/database.php';
require_once '../objects/user.php';
require_once '../objects/note.php';
require_once '../objects/department.php';

session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$note = new Note($db);
$department = new Department($db);

$users_count = $user->count();
$notes_count = $note->count();
$departments_count = $department->count();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <?php include 'partials/nav.php'; ?>
        
        <div class="flex-1">
            <?php include 'partials/header.php'; ?>
            
            <main class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-2">Utilisateurs</h3>
                        <p class="text-3xl font-bold text-blue-600"><?= $users_count ?></p>
                        <a href="users.php" class="text-blue-500 hover:underline mt-2 block">Gérer les utilisateurs</a>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-2">Notes</h3>
                        <p class="text-3xl font-bold text-green-600"><?= $notes_count ?></p>
                        <a href="notes.php" class="text-blue-500 hover:underline mt-2 block">Gérer les notes</a>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-2">Départements</h3>
                        <p class="text-3xl font-bold text-yellow-600"><?= $departments_count ?></p>
                        <a href="departments.php" class="text-blue-500 hover:underline mt-2 block">Gérer les départements</a>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Dernières notes</h2>
                    <?php
                    $latest_notes = $note->read(5);
                    if($latest_notes->rowCount() > 0):
                    ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auteur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while($note_row = $latest_notes->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($note_row['title']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $note_row['type'] === 'urgent' ? 'bg-red-100 text-red-800' : 
                                               ($note_row['type'] === 'all' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                            <?= ucfirst($note_row['type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($note_row['author_name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($note_row['created_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-500">Aucune note disponible</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>