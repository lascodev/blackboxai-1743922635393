<?php
require_once '../config/database.php';
require_once '../objects/note.php';
require_once '../objects/department.php';
require_once '../objects/user.php';

session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$note = new Note($db);
$department = new Department($db);
$user = new User($db);

$id = $_GET['id'] ?? 0;
if($id > 0) {
    $note->id = $id;
    if(!$note->readOne()) {
        header('Location: notes.php');
        exit;
    }
}

$departments = $department->read();
$authors = $user->readAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note->title = $_POST['title'];
    $note->content = $_POST['content'];
    $note->type = $_POST['type'];
    $note->department_id = $_POST['department_id'];
    $note->author_id = $_POST['author_id'];
    
    if($id > 0) {
        // Mise à jour
        $note->update();
    } else {
        // Création
        $note->create();
    }
    
    header('Location: notes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> Note</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <?php include 'partials/nav.php'; ?>
        
        <div class="flex-1">
            <?php include 'partials/header.php'; ?>
            
            <main class="p-6">
                <h2 class="text-2xl font-bold mb-6"><?= $id > 0 ? 'Modifier' : 'Ajouter' ?> une note</h2>
                
                <form method="POST" class="bg-white rounded-lg shadow p-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="title">Titre</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($note->title ?? '') ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="content">Contenu</label>
                        <textarea id="content" name="content" rows="6" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($note->content ?? '') ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2" for="type">Type</label>
                            <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="urgent" <?= ($note->type ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                <option value="all" <?= ($note->type ?? '') === 'all' ? 'selected' : '' ?>>Général</option>
                                <option value="department" <?= ($note->type ?? '') === 'department' ? 'selected' : '' ?>>Département</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2" for="department_id">Département</label>
                            <select id="department_id" name="department_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">-- Sélectionner --</option>
                                <?php while($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $dept['id'] ?>" <?= ($note->department_id ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2" for="author_id">Auteur</label>
                            <select id="author_id" name="author_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">-- Sélectionner --</option>
                                <?php while($auth = $authors->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $auth['id'] ?>" <?= ($note->author_id ?? '') == $auth['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($auth['username']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <a href="notes.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
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