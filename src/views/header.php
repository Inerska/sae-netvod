<nav class="border-gray-200 px-2 sm:px-4 py-2.5 dark:bg-gray-900 bg-gray-50">
    <div class="container flex flex-wrap justify-between items-center mx-auto">
        <a href="index.php" class="flex items-center">
            <span class="self-center text-xl font-semibold whitespace-nowrap text-red-600 rounded">NETVOD</span>
        </a>
        <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg md:hidden focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="navbar-default" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                      d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                      clip-rule="evenodd"></path>
            </svg>
        </button>
        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <ul class="flex flex-col p-4 mt-4 rounded-lg border md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0 dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                <?php if (isset($_SESSION['loggedUser'])): ?>
                    <li class="flex flex-col md:flex-row">
                        <a href="?action=search"
                           class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                            <span>
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </a>
                    </li>
                    <li class="flex flex-col md:flex-row">
                        <a href="?action=viewCatalogue"
                           class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                            <span>Catalogue</span>
                        </a>
                    </li>
                    <li class="flex flex-col md:flex-row">
                        <a href="?action=profile"
                           class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                            <span>Mon compte</span>
                        </a>
                    <li class="flex flex-col md:flex-row">
                        <a href="?action=sign-out"
                           class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                            <span>Déconnexion</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="flex flex-col md:flex-row">
                        <a href="?action=sign-in"
                           class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                            <span>Connexion</span>
                        </a>
                    </li>
                    <li class="flex flex-col md:flex-row">
                        <a href="?action=sign-up"
                           class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                            <span>Inscription</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="flex flex-col md:flex-row">
                    <button id="theme-toggle"
                       class="flex items-center px-4 py-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400">
                        <i class="fa-solid fa-moon w-5 h-5" id="theme-toggle-dark-icon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>