<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
        <div class="flex items-center space-x-4">
            <span class="text-gray-600"><?= $_SESSION['user']['username'] ?></span>
            <a href="logout.php" class="text-red-500 hover:text-red-700">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</header>