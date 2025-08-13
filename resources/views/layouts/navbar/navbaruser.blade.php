<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-fluid">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="/" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('assets/img/bogasari.png') }}" alt="" width="32px" height="22px"
                        srcset="">
                </span>
                <span class="app-brand-text demo menu-text fw-bold">BMS</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="ti ti-x ti-sm align-middle"></i>
            </a>
        </div>
        <div class="navbar-nav-left d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                {{-- Dashboard Dropdown --}}
                <li class="nav-item me-2 ">
                    <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'text-primary' : '' }}"
                        href="{{ route('user.dashboard') }}">
                        <span>Dashboard</span>
                    </a>
                </li>
                {{-- Asset Analysis Dropdown --}}
                <li class="nav-item navbar-dropdown dropdown me-2">
                    <a class="nav-link dropdown-toggle hide-arrow {{ request()->routeIs('user.asset-analysis') || request()->routeIs('user.cross-asset-analysis') || request()->routeIs('user.asset-information') ? 'text-primary' : '' }}"
                        href="javascript:void(0);" data-bs-toggle="dropdown">
                        <span>Asset Analysis</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('user.asset-information') }}">
                                <span class="align-middle">Asset Information</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.asset-analysis') }}">
                                <span class="align-middle">Asset Performance</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.cross-asset-analysis') }}">
                                <span class="align-middle">Cross Asset Analysis</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item navbar-dropdown dropdown me-2">
                    <a class="nav-link dropdown-toggle hide-arrow {{ request()->routeIs('alert-asset') || request()->routeIs('historical-alert') ? 'text-primary' : '' }}"
                        href="javascript:void(0);" data-bs-toggle="dropdown">
                        <span>Report Alert</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('alert-asset') }}">
                                <span class="align-middle">Active Alert</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('historical-alert') }}">
                                <span class="align-middle">Historical Alert</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                {{-- Dashboard Dropdown --}}


                {{-- Theme Toggle --}}
                <li class="nav-item me-2 me-xl-0">
                    <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                        <i class="ti ti-md"></i>
                    </a>
                </li>
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="h-auto rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="pages-account-settings-account.html">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="../../assets/img/avatars/1.png" alt
                                                class="h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                        <small class="text-muted"></small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                <i class="ti ti-logout me-2 ti-sm"></i>
                                <span class="align-middle">Log Out</span>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </div>
</nav>
