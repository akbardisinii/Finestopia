<aside class="vertical-nav bg-white" id="sidebar">
    <div class="py-4 px-3 mb-4 bg-light">
        <div class="media d-flex align-items-center">
        <img loading="lazy" src="{{ asset('logo.png') }}" alt="robot icon" width="80" height="80"class="mr-3 rounded-circle img-thumbnail shadow-sm">
            <div class="media-body">
                <h4 class="">{{ Auth::user()->name }}</h4>
                <div class="mt-1"> <!-- Menambahkan margin top untuk jarak -->
                    <i class="fa-solid fa-briefcase mr-1"></i> <!-- Memperbaiki icon fa-circle -->
                    <small class="font-weight-normal text-muted mb-0">{{ Auth::user()->job }}</small>
                </div>
            </div>
        </div>
    </div>

    <p class="text-gray font-weight-bold text-uppercase px-3 small mb-0">Dashboard</p>

    <ul class="nav flex-column bg-white mb-0">
        <li class="nav-item">
            <a href="{{ url('api/home') }}"
                class="nav-link text-dark {{ request()->is('api/home') ? 'bg-light' : "" }}">
                <i class="fa-solid fa-home mr-2" style="color: #fd7e14;"></i>
                Beranda
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/balances/index') }}"
                class="nav-link text-dark {{ request()->is('api/balances/index') ? 'bg-light' : "" }}">
                <i class="fa-solid fa-dollar-sign mr-3" style="color: #fd7e14;"></i>
                Saldo
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/kalkulator') }}"
                class="nav-link text-dark {{ request()->is('api/kalkulator') ? 'bg-light' : "" }}">
                <i class="fa-solid fa-calculator mr-3" style="color: #fd7e14;"></i>
                Kalkulator
            </a>
        </li>
        <li class="nav-item">
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="nav-link text-dark">
            <i class="fa fa-sign-out mr-3" style="color: #fd7e14;"></i>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </li>
    </ul>

    <!-- <p class="text-gray font-weight-bold text-uppercase px-3 pt-2 small mb-0">Budget</p>
    <ul class="nav flex-column bg-white mb-0">
        <li class="nav-item">
            <a href="{{ url('api/budgets/current') }}"
                class="nav-link text-dark {{ request()->is('api/budgets/current') ? "bg-light" : "" }}">
                <i class="fa fa-check mr-3 text-primary fa-fw"></i>
                Active Budget
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/budgets/create') }}"
                class="nav-link text-dark {{ request()->is('api/budgets/create') ? "bg-light" : "" }}">
                <i class="fa fa-plus-circle mr-3 text-primary fa-fw"></i>
                Create a New One
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/budgets/index') }}"
                class="nav-link text-dark {{ request()->is('api/budgets/index') ? "bg-light" : "" }}">
                <i class="fa fa-table mr-3 text-primary fa-fw"></i>
                View All Budgets
            </a>
        </li>
    </ul>

    <p class="text-gray font-weight-bold text-uppercase px-3 pt-2 small mb-0">Category</p>
    <ul class="nav flex-column bg-white mb-0">
        <li class="nav-item">
            <a href="{{ url('api/categories/create') }}"
                class="nav-link text-dark {{ request()->is('api/categories/create') ? "bg-light" : "" }}">
                <i class="fa fa-plus-circle mr-3 text-primary fa-fw"></i>
                Create a New One
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/categories/index') }}"
                class="nav-link text-dark {{ request()->is('api/categories/index') ? "bg-light" : "" }}">
                <i class="fa fa-table mr-3 text-primary fa-fw"></i>
                View All Categories
            </a>
        </li>
    </ul> -->

    <!-- <p class="text-gray font-weight-bold text-uppercase px-3 pt-2 small mb-0">Payment Options</p>
    <ul class="nav flex-column bg-white mb-0">
        <li class="nav-item">
            <a href="{{ url('api/payment-options/create') }}" class="nav-link text-dark">
                <i class="fa fa-plus-circle mr-3 text-primary fa-fw"></i>
                Create a New One
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/payment-options/index') }}" class="nav-link text-dark">
                <i class="fa fa-table mr-3 text-primary fa-fw"></i>
                View Payment Options
            </a>
        </li>
    </ul>

    <p class="text-gray font-weight-bold text-uppercase px-3 pt-2 small mb-0">Transactions</p>
    <ul class="nav flex-column bg-white mb-0">
        <li class="nav-item">
            <a href="{{ url('api/transactions/create') }}" class="nav-link text-dark">
                <i class="fa fa-plus-circle mr-3 text-primary fa-fw"></i>
                Create a New One
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('api/transactions/index') }}" class="nav-link text-dark">
                <i class="fa fa-table mr-3 text-primary fa-fw"></i>
                View Transactions
            </a>
        </li>
    </ul> -->
    

</aside>