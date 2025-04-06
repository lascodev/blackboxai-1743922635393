<?php
require_once '../config/database.php';
require_once '../objects/user.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];

    if($user->login() && $user->role === 'admin') {
        session_start();
        $_SESSION['user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'department_id' => $user->department_id,
            'department_name' => $user->department_name
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = "Identifiants incorrects ou accès non autorisé";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6">Connexion Admin</h1>
            <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $error ?>
            </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2" for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                    <i class="fas fa-sign-in-alt mr-2"></i> Se connecter
                </button>
            </form>
        </div>
    </div>
</body>
</html>