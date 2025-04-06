<?php
require_once '../config/database.php';
require_once '../objects/note.php';

session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$note = new Note($db);

// Gestion des actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if($action === 'delete' && $id > 0) {
    $note->id = $id;
    $note->delete();
    header('Location: notes.php');
    exit;
}

$notes = $note->read();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Notes</title>
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
                    <h2 class="text-2xl font-bold">Gestion des Notes</h2>
                    <a href="note_edit.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i> Ajouter une note
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auteur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($row = $notes->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $row['type'] === 'urgent' ? 'bg-red-100 text-red-800' : 
                                           ($row['type'] === 'all' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        <?= ucfirst($row['type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['author_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['department_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="note_edit.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="notes.php?action=delete&id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Supprimer cette note ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php if($row['pdf_path']): ?>
                                    <a href="../<?= $row['pdf_path'] ?>" target="_blank" class="text-green-500 hover:text-green-700">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <?php endif; ?>
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