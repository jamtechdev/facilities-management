@php
    $user = auth()->user();
@endphp

<nav class="sidebar-nav">
    @if ($user->hasAnyRole(['SuperAdmin', 'Admin']))
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            @can('view admin dashboard')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('dashboard') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('dashboard') }}">
                        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
            @endcan
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('settings') ? 'active' : '' }}"
                    href="{{ \App\Helpers\RouteHelper::url('settings') }}">
                    <span class="nav-icon"><i class="bi bi-gear"></i></span>
                    <span class="nav-text">Settings</span>
                </a>
            </div>
        </div>

        @if($user->can('view leads') || $user->can('view clients') || $user->can('view staff'))
        <div class="nav-section">
            <div class="nav-section-title">Business</div>
            @can('view leads')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('leads.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('leads.index') }}">
                        <span class="nav-icon"><i class="bi bi-person-lines-fill"></i></span>
                        <span class="nav-text">Leads</span>
                    </a>
                </div>
            @endcan

            @can('view clients')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('clients.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('clients.index') }}">
                        <span class="nav-icon"><i class="bi bi-building"></i></span>
                        <span class="nav-text">Clients</span>
                    </a>
                </div>
            @endcan

            @can('view staff')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('staff.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('staff.index') }}">
                        <span class="nav-icon"><i class="bi bi-people"></i></span>
                        <span class="nav-text">Staff</span>
                    </a>
                </div>
            @endcan
        </div>
        @endif

        @if($user->can('view invoices') || $user->can('view payouts'))
        <div class="nav-section">
            <div class="nav-section-title">Financial</div>
            @can('view invoices')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('invoices.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('invoices.index') }}">
                        <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                        <span class="nav-text">Invoices</span>
                    </a>
                </div>
            @endcan

            @can('view payouts')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('payouts.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('payouts.index') }}">
                        <span class="nav-icon"><i class="bi bi-cash-coin"></i></span>
                        <span class="nav-text">Payouts</span>
                    </a>
                </div>
            @endcan
        </div>
        @endif

        @if($user->can('view inventory'))
        <div class="nav-section">
            <div class="nav-section-title">Operations</div>
            @can('view inventory')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('inventory.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('inventory.index') }}">
                        <span class="nav-icon"><i class="bi bi-box-seam"></i></span>
                        <span class="nav-text">Inventory</span>
                    </a>
                </div>
            @endcan
        </div>
        @endif

        @if($user->can('view roles') || $user->can('view users'))
        <div class="nav-section">
            <div class="nav-section-title">System</div>
            @can('view roles')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('roles.*') || \App\Helpers\RouteHelper::routeIsAny('permissions.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('roles.index') }}">
                        <span class="nav-icon"><i class="bi bi-shield-check"></i></span>
                        <span class="nav-text">Roles & Permissions</span>
                    </a>
                </div>
            @endcan

            @can('view users')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('users.*') ? 'active' : '' }}"
                        href="{{ \App\Helpers\RouteHelper::url('users.index') }}">
                        <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                        <span class="nav-text">Users</span>
                    </a>
                </div>
            @endcan
        </div>
        @endif
    @elseif($user->hasRole('Staff'))
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            @can('view staff dashboard')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"
                        href="{{ route('staff.dashboard') }}">
                        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
            @endcan

            @can('view timesheets')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('staff.timesheet*') ? 'active' : '' }}"
                        href="{{ route('staff.timesheet') }}">
                        <span class="nav-icon"><i class="bi bi-clock-history"></i></span>
                        <span class="nav-text">Timesheet</span>
                    </a>
                </div>
            @endcan

            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('staff.activity') ? 'active' : '' }}"
                    href="{{ route('staff.activity') }}">
                    <span class="nav-icon"><i class="bi bi-activity"></i></span>
                    <span class="nav-text">Activity Log</span>
                </a>
            </div>
        </div>
    @elseif($user->hasRole('Client'))
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.dashboard') ? 'active' : '' }}"
                    href="{{ route('client.dashboard') }}">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Services</div>
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.services*') ? 'active' : '' }}"
                    href="#">
                    <span class="nav-icon"><i class="bi bi-calendar-check"></i></span>
                    <span class="nav-text">Service History</span>
                </a>
            </div>
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.photos*') ? 'active' : '' }}"
                    href="#">
                    <span class="nav-icon"><i class="bi bi-images"></i></span>
                    <span class="nav-text">Before & After Photos</span>
                </a>
            </div>
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.feedback*') ? 'active' : '' }}"
                    href="#">
                    <span class="nav-icon"><i class="bi bi-chat-dots"></i></span>
                    <span class="nav-text">Feedback</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Documents</div>
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.documents*') ? 'active' : '' }}"
                    href="#">
                    <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span>
                    <span class="nav-text">Documents</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Financial</div>
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.invoices*') ? 'active' : '' }}"
                    href="#">
                    <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                    <span class="nav-text">Invoices</span>
                </a>
            </div>
            @if (Route::has('client.inventory.index'))
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('client.inventory*') ? 'active' : '' }}"
                        href="#">
                        <span class="nav-icon"><i class="bi bi-box-seam"></i></span>
                        <span class="nav-text">Inventory</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

</nav>

<div class="sidebar-footer">
    {{-- <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="user-details">
            <div class="user-name">{{ $user->name }}</div>
            <div class="user-role">
                @php
                    if($user->hasRole('Admin')) {
                        $roleName = 'Super Admin';
                    } elseif($user->hasRole('SuperAdmin')) {
                        $roleName = 'Admin';
                    } elseif($user->hasRole('Staff')) {
                        $roleName = 'Staff';
                    } else {
                        $roleName = 'User';
                    }
                @endphp
                {{ $roleName }}
            </div>
        </div>
    </div> --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-light btn-sm w-100">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </button>
    </form>
</div>
