<nav class="navbar-top sticky-top">
    <div class="navbar-top-container">
        <div class="navbar-top-left">
            <button class="btn-sidebar-toggle" type="button" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-chevron-left" id="sidebarToggleIcon"></i>
            </button>
            <div class="navbar-breadcrumb">
                <span class="breadcrumb-item">@yield('title', 'Dashboard')</span>
            </div>
        </div>

        <div class="navbar-top-right">
            <div class="navbar-icon-wrapper" data-bs-toggle="tooltip" title="Notifications">
                <button class="navbar-icon-btn" type="button" id="notificationsBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    @php
                        $notificationCount = \App\Models\FollowUpTask::where('is_completed', false)
                            ->where('due_date', '<=', \Carbon\Carbon::now()->addDays(7))
                            ->count();
                    @endphp
                    @if($notificationCount > 0)
                        <span class="navbar-badge">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <strong>Notifications</strong>
                        <small class="text-muted">Follow-up Reminders</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div id="notificationsList">
                        @php
                            $notifications = \App\Models\FollowUpTask::where('is_completed', false)
                                ->where('due_date', '<=', \Carbon\Carbon::now()->addDays(7))
                                ->with(['lead.assignedStaff'])
                                ->orderBy('due_date', 'asc')
                                ->take(10)
                                ->get();
                        @endphp
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
                                @php
                                    $isOverdue = $notification->due_date->isPast();
                                    $daysUntil = $notification->due_date->diffInDays(\Carbon\Carbon::now(), false);
                                @endphp
                                <a href="{{ \App\Helpers\RouteHelper::url('leads.show', $notification->lead) }}" class="dropdown-item notification-item {{ $isOverdue ? 'notification-overdue' : '' }}">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-calendar-event text-{{ $isOverdue ? 'danger' : 'warning' }}"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-bold">{{ $notification->lead->name }}</div>
                                            <small class="text-muted">{{ Str::limit($notification->suggestion, 50) }}</small>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    Day {{ $notification->reminder_day }} - {{ $notification->due_date->format('M d, Y') }}
                                                    @if($isOverdue)
                                                        <span class="badge bg-danger ms-1">{{ abs($daysUntil) }}d overdue</span>
                                                    @elseif($daysUntil <= 3)
                                                        <span class="badge bg-warning ms-1">{{ $daysUntil }}d left</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                            @endforeach
                        @else
                            <div class="dropdown-item text-center py-3">
                                <i class="bi bi-check-circle text-success notification-icon-large"></i>
                                <p class="text-muted mt-2 mb-0">No notifications</p>
                            </div>
                        @endif
                    </div>
                    @if($notifications->count() > 10)
                        <div class="dropdown-footer text-center py-2">
                            <a href="{{ \App\Helpers\RouteHelper::url('dashboard') }}" class="text-decoration-none small">View all notifications</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="navbar-user-dropdown">
                <button class="navbar-user-btn" type="button" id="userDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="navbar-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="navbar-user-info">
                        <div class="navbar-user-name">{{ auth()->user()->name }}</div>
                        <div class="navbar-user-role">
                            @php
                                $user = auth()->user();
                                $role = $user->roles->first();
                                if($role) {
                                    $roleName = $role->name;
                                    // Map role names to colors
                                    $roleColorMap = [
                                        'SuperAdmin' => 'danger',
                                        'Admin' => 'primary',
                                        'Staff' => 'success',
                                        'Client' => 'info',
                                        'Lead' => 'warning',
                                    ];
                                    $roleColor = $roleColorMap[$roleName] ?? 'secondary';
                                } else {
                                    $roleName = 'User';
                                    $roleColor = 'secondary';
                                }
                            @endphp
                            <span class="badge bg-{{ $roleColor }}">{{ $roleName }}</span>
                        </div>
                    </div>
                </button>
                <div class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                    <h6 class="dropdown-header">Account</h6>
                    <a class="dropdown-item user-dropdown-item" href="{{ \App\Helpers\RouteHelper::url('profile') }}">
                        <i class="bi bi-person-circle me-2"></i>Profile
                    </a>
                    @if(auth()->user()->can('view admin dashboard'))
                        <a class="dropdown-item user-dropdown-item {{ \App\Helpers\RouteHelper::routeIsAny('settings') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('settings') }}">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item user-dropdown-item logout-item" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
