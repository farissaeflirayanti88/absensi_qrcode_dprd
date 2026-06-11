<!-- Header -->
<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left: Mobile menu + Title -->
            <div class="flex items-center">
                <button id="mobileSidebarToggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                
                <!-- Page Title -->
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h2>
                    @hasSection('page-subtitle')
                    <p class="text-xs text-gray-500">
                        @yield('page-subtitle')
                    </p>
                    @endif
                </div>
            </div>
            
            <!-- Right: User Dropdown -->
            <div class="flex items-center">
                <div class="relative">
                    <button id="userDropdownBtn" 
                            class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">
                                @auth
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                @else
                                    GU
                                @endauth
                            </span>
                        </div>
                        <div class="hidden lg:block text-left">
                            <p class="text-sm font-medium text-gray-700">
                                @auth
                                    {{ auth()->user()->name }}
                                @else
                                    Guest
                                @endauth
                            </p>
                            <p class="text-xs text-gray-500">
                                @auth
                                    {{ auth()->user()->role ?? 'Administrator' }}
                                @else
                                    Guest
                                @endauth
                            </p>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-500 hidden lg:block"></i>
                    </button>
                    
                    <!-- User Dropdown Menu -->
                    <div id="userDropdown" 
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                Profile Saya
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            
                             <form method="POST" action="{{ route('logout') }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Keluar
                                </button>
                            </form>
							</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Sidebar Toggle
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
        });
    }
    
    // User Dropdown
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdownBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
        
        // Close dropdown when clicking on a link
        const dropdownLinks = userDropdown.querySelectorAll('a');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function() {
                userDropdown.classList.add('hidden');
            });
        });
    }
});
</script>