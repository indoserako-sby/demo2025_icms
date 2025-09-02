<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="/" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/is.png') }}" alt="" width="32px" height="22px" srcset="">
            </span>
            <span class="app-brand-text demo menu-text fw-bold">ICMS</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item  {{ request()->is('dashboard*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard"></div>
            </a>
        </li>
        {{-- master  --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
        </li>
        <li class="menu-item {{ request()->is('admin/area*') ? 'active' : '' }}">
            <a href="{{ route('area.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-map"></i>
                <div data-i18n="Area">Area</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('admin/group*') ? 'active' : '' }}">
            <a href="{{ route('group.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Group">Group</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('admin/asset*') ? 'active' : '' }}">
            <a href="{{ route('asset.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-package"></i>
                <div data-i18n="Asset">Asset</div>
            </a>
        </li>
        <li
            class="menu-item {{ request()->is('admin/machine-parameter*') || request()->is('admin/position*') ? 'active' : '' }}">
            <a href="{{ route('machine-parameter.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Parameter">Parameter & Position</div>
            </a>
        </li>
        {{-- menu variabel --}}
        <li
            class="menu-item {{ request()->is('admin/datvar*') || request()->is('admin/datactual*') ? 'active' : '' }}">
            <a href="{{ route('datvar.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-database"></i>
                <div data-i18n="Variable & Actual">Variable & Actual</div>
            </a>
        </li>
        {{-- List Data  --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">List Data</span>
        </li>
        <li class="menu-item {{ request()->is('admin/list-data*') ? 'active' : '' }}">
            <a href="{{ route('list-data.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div data-i18n="Asset Data">Asset Data</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Administration</span>
        </li>
        <li class="menu-item {{ request()->is('admin/user-management*') ? 'active' : '' }}">
            <a href="{{ route('user-management.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Users Management">Users Management</div>
            </a>
        </li>

    </ul>
</aside>
