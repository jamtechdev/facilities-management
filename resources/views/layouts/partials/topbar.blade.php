<nav class="navbar-top sticky-top">
    <div class="navbar-top-container">
        <div class="navbar-top-left">
            <button class="btn-sidebar-toggle d-md-none" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i>
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
                <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="min-width: 350px; max-width: 400px; max-height: 500px; overflow-y: auto;">
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
                                <a href="{{ route('admin.leads.show', $notification->lead) }}" class="dropdown-item notification-item {{ $isOverdue ? 'notification-overdue' : '' }}">
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
                                <i class="bi bi-check-circle text-success" style="font-size: 32px;"></i>
                                <p class="text-muted mt-2 mb-0">No notifications</p>
                            </div>
                        @endif
                    </div>
                    @if($notifications->count() > 10)
                        <div class="dropdown-footer text-center py-2">
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none small">View all notifications</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="navbar-icon-wrapper" data-bs-toggle="tooltip" title="Messages">
                <button class="navbar-icon-btn" type="button" id="messagesBtn">
                    <i class="bi bi-envelope"></i>
                    <span class="navbar-badge">5</span>
                </button>
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
                                if(auth()->user()->hasRole('Admin')) {
                                    $roleName = 'Super Admin';
                                    $roleColor = 'danger';
                                } elseif(auth()->user()->hasRole('SuperAdmin')) {
                                    $roleName = 'Admin';
                                    $roleColor = 'primary';
                                } elseif(auth()->user()->hasRole('Staff')) {
                                    $roleName = 'Staff';
                                    $roleColor = 'success';
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
                    @php
                        $profileRoute = 'admin.profile';
                        if(auth()->user()->hasRole('SuperAdmin')) {
                            $profileRoute = 'superadmin.profile';
                        } elseif(auth()->user()->hasRole('Admin')) {
                            $profileRoute = 'admin.profile';
                        } elseif(auth()->user()->hasRole('Staff')) {
                            $profileRoute = 'staff.profile';
                        } elseif(auth()->user()->hasRole('Client')) {
                            $profileRoute = 'client.profile';
                        }
                    @endphp
                    <a class="dropdown-item user-dropdown-item" href="{{ route($profileRoute) }}">
                        <i class="bi bi-person-circle me-2"></i>Profile
                    </a>
                    <a class="dropdown-item user-dropdown-item" href="#">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
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

