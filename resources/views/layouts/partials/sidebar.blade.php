@php
    $user = auth()->user();
@endphp

<nav class="sidebar-nav">
    @if ($user->hasAnyRole(['Admin', 'SuperAdmin']))
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            @can('view admin dashboard')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"
                        href="{{ $user->hasRole('SuperAdmin') ? route('superadmin.dashboard') : route('admin.dashboard') }}">
                        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
            @endcan
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('admin.profile*') || request()->routeIs('superadmin.profile*') ? 'active' : '' }}"
                    href="{{ $user->hasRole('SuperAdmin') ? route('superadmin.profile') : route('admin.profile') }}">
                    <span class="nav-icon"><i class="bi bi-person"></i></span>
                    <span class="nav-text">Profile</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Business</div>
            @can('view leads')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}"
                        href="{{ route('admin.leads.index') }}">
                        <span class="nav-icon"><i class="bi bi-person-lines-fill"></i></span>
                        <span class="nav-text">Leads</span>
                    </a>
                </div>
            @endcan

            @can('view clients')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}"
                        href="{{ route('admin.clients.index') }}">
                        <span class="nav-icon"><i class="bi bi-building"></i></span>
                        <span class="nav-text">Clients</span>
                    </a>
                </div>
            @endcan

            @can('view staff')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"
                        href="{{ route('admin.staff.index') }}">
                        <span class="nav-icon"><i class="bi bi-people"></i></span>
                        <span class="nav-text">Staff</span>
                    </a>
                </div>
            @endcan
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Financial</div>
            @can('view invoices')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
                        href="{{ route('admin.invoices.index') }}">
                        <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                        <span class="nav-text">Invoices</span>
                    </a>
                </div>
            @endcan

            @can('view payouts')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}"
                        href="{{ route('admin.payouts.index') }}">
                        <span class="nav-icon"><i class="bi bi-cash-coin"></i></span>
                        <span class="nav-text">Payouts</span>
                    </a>
                </div>
            @endcan
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Operations</div>
            @can('view inventory')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                        href="{{ route('admin.inventory.index') }}">
                        <span class="nav-icon"><i class="bi bi-box-seam"></i></span>
                        <span class="nav-text">Inventory</span>
                    </a>
                </div>
            @endcan
        </div>

        <div class="nav-section">
            <div class="nav-section-title">System</div>
            @can('view roles')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        <span class="nav-icon"><i class="bi bi-shield-check"></i></span>
                        <span class="nav-text">Roles & Permissions</span>
                    </a>
                </div>
            @endcan

            @can('view users')
                <div class="nav-item-modern">
                    <a class="nav-link-modern {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                        <span class="nav-text">Users</span>
                    </a>
                </div>
            @endcan
        </div>
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
                <a class="nav-link-modern {{ request()->routeIs('staff.profile*') ? 'active' : '' }}"
                    href="{{ route('staff.profile') }}">
                    <span class="nav-icon"><i class="bi bi-person"></i></span>
                    <span class="nav-text">Profile</span>
                </a>
            </div>
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
            <div class="nav-item-modern">
                <a class="nav-link-modern {{ request()->routeIs('client.profile*') ? 'active' : '' }}"
                    href="{{ route('client.profile') }}">
                    <span class="nav-icon"><i class="bi bi-person"></i></span>
                    <span class="nav-text">Profile</span>
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
                    if($user->hasRole('SuperAdmin')) {
                        $roleName = 'Super Admin';
                    } elseif($user->hasRole('Admin')) {
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
