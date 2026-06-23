<aside class="sidebar">

    <div class="sidebar-logo">
        <i class="fa-solid fa-mug-hot me-2"></i>
        Cafe POS
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active-link' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i>
            Dashboard
        </a>

        <div class="nav-title">
            Management
        </div>

        <a href="{{ route('categories.index')}}" class="nav-link {{ request()->routeIs('categories.index') ? 'active-link' : '' }}">
            <i class="fa-solid fa-layer-group me-2"></i>
            Categories
        </a>

        <a href="{{ route('products.index')}}" class="nav-link {{ request()->routeIs('products.index') ? 'active-link' : '' }}">
            <i class="fa-solid fa-box-open me-2"></i>
            Products
        </a>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-tags me-2"></i>
            Variants
        </a>

        <div class="nav-title">
            Operations
        </div>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-cash-register me-2"></i>
            POS
        </a>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-receipt me-2"></i>
            Sales
        </a>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-utensils me-2"></i>
            Kitchen
        </a>

        <div class="nav-title">
            Administration
        </div>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-users me-2"></i>
            Users
        </a>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-gear me-2"></i>
            Settings
        </a>

        <a href="#" class="nav-link">
            <i class="fa-solid fa-envelope me-2"></i>
            Reports
        </a>
    </nav>

</aside>