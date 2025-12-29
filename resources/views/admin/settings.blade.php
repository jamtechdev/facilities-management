@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
    @vite(['resources/css/profile.css'])
    <style>
        .settings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .settings-card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .settings-card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #495057;
        }
        .settings-card-body {
            padding: 1.5rem;
        }
        .form-check-input:checked {
            background-color: #84c373;
            border-color: #84c373;
        }
        .form-check-input:focus {
            border-color: #84c373;
            box-shadow: 0 0 0 0.25rem rgba(132, 195, 115, 0.25);
        }
        .settings-section {
            margin-bottom: 2rem;
        }
        .settings-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #84c373;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Settings Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="bi bi-gear-fill icon-2rem"></i>
            </div>
            <div class="profile-info">
                <h1>Settings</h1>
                <p>Manage your notifications, messages, and project preferences</p>
            </div>
        </div>
    </div>

    <div id="alert-container"></div>

    <ul class="nav nav-tabs profile-tabs" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notificationsTab" type="button" role="tab">
                <i class="bi bi-bell me-2"></i>Notifications
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messagesTab" type="button" role="tab">
                <i class="bi bi-chat-dots me-2"></i>Messages
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#generalTab" type="button" role="tab">
                <i class="bi bi-sliders me-2"></i>General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="project-tab" data-bs-toggle="tab" data-bs-target="#projectTab" type="button" role="tab">
                <i class="bi bi-folder me-2"></i>Project
            </button>
        </li>
    </ul>

    <div class="tab-content mt-4">
        <!-- Notifications Tab -->
        <div class="tab-pane fade show active" id="notificationsTab" role="tabpanel" aria-labelledby="notifications-tab">
            <form id="notificationsForm">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h5><i class="bi bi-bell-fill me-2"></i>Notification Preferences</h5>
                    </div>
                    <div class="settings-card-body">
                        <div class="settings-section">
                            <div class="settings-section-title">Notification Channels</div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" {{ $settings->email_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    <strong>Email Notifications</strong>
                                    <small class="d-block text-muted">Receive notifications via email</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="in_app_notifications" name="in_app_notifications" {{ $settings->in_app_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="in_app_notifications">
                                    <strong>In-App Notifications</strong>
                                    <small class="d-block text-muted">Show notifications in the application</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" {{ $settings->sms_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_notifications">
                                    <strong>SMS Notifications</strong>
                                    <small class="d-block text-muted">Receive notifications via SMS (if configured)</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications" {{ $settings->push_notifications ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="push_notifications">
                                    <strong>Push Notifications</strong>
                                    <small class="d-block text-muted">Receive push notifications via Firebase (requires browser permission)</small>
                                </label>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-section-title">What to Notify Me About</div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_new_leads" name="notify_new_leads" {{ $settings->notify_new_leads ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_new_leads">
                                    <strong>New Leads</strong>
                                    <small class="d-block text-muted">When a new lead is created</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_lead_updates" name="notify_lead_updates" {{ $settings->notify_lead_updates ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_lead_updates">
                                    <strong>Lead Updates</strong>
                                    <small class="d-block text-muted">When lead information is updated</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_client_updates" name="notify_client_updates" {{ $settings->notify_client_updates ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_client_updates">
                                    <strong>Client Updates</strong>
                                    <small class="d-block text-muted">When client information changes</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_staff_updates" name="notify_staff_updates" {{ $settings->notify_staff_updates ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_staff_updates">
                                    <strong>Staff Updates</strong>
                                    <small class="d-block text-muted">When staff information is updated</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_invoice_updates" name="notify_invoice_updates" {{ $settings->notify_invoice_updates ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_invoice_updates">
                                    <strong>Invoice Updates</strong>
                                    <small class="d-block text-muted">When invoices are created or updated</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_follow_up_tasks" name="notify_follow_up_tasks" {{ $settings->notify_follow_up_tasks ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_follow_up_tasks">
                                    <strong>Follow-up Tasks</strong>
                                    <small class="d-block text-muted">Reminders for follow-up tasks</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_communications" name="notify_communications" {{ $settings->notify_communications ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_communications">
                                    <strong>Communications</strong>
                                    <small class="d-block text-muted">New communications and messages</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_document_uploads" name="notify_document_uploads" {{ $settings->notify_document_uploads ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_document_uploads">
                                    <strong>Document Uploads</strong>
                                    <small class="d-block text-muted">When documents are uploaded</small>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Notification Settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Messages Tab -->
        <div class="tab-pane fade" id="messagesTab" role="tabpanel" aria-labelledby="messages-tab">
            <form id="messagesForm">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h5><i class="bi bi-chat-dots-fill me-2"></i>Message Preferences</h5>
                    </div>
                    <div class="settings-card-body">
                        <div class="settings-section">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="receive_messages" name="receive_messages" {{ $settings->receive_messages ? 'checked' : '' }}>
                                <label class="form-check-label" for="receive_messages">
                                    <strong>Receive Messages</strong>
                                    <small class="d-block text-muted">Allow others to send you messages</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_on_message" name="email_on_message" {{ $settings->email_on_message ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_on_message">
                                    <strong>Email on New Message</strong>
                                    <small class="d-block text-muted">Receive email when you get a new message</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notify_message_read" name="notify_message_read" {{ $settings->notify_message_read ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_message_read">
                                    <strong>Read Receipts</strong>
                                    <small class="d-block text-muted">Notify senders when you read their messages</small>
                                </label>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-section-title">Message Frequency</div>
                            <div class="mb-3">
                                <label for="message_frequency" class="form-label">How often should we notify you about messages?</label>
                                <select class="form-select" id="message_frequency" name="message_frequency">
                                    <option value="immediate" {{ $settings->message_frequency === 'immediate' ? 'selected' : '' }}>Immediately</option>
                                    <option value="daily" {{ $settings->message_frequency === 'daily' ? 'selected' : '' }}>Daily Digest</option>
                                    <option value="weekly" {{ $settings->message_frequency === 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                                </select>
                                <small class="form-text text-muted">Choose how often you want to be notified about new messages</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Message Settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- General Tab -->
        <div class="tab-pane fade" id="generalTab" role="tabpanel" aria-labelledby="general-tab">
            <form id="generalForm">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h5><i class="bi bi-sliders me-2"></i>General Preferences</h5>
                    </div>
                    <div class="settings-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC" {{ $settings->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ $settings->timezone === 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                                    <option value="America/Chicago" {{ $settings->timezone === 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                                    <option value="America/Denver" {{ $settings->timezone === 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                                    <option value="America/Los_Angeles" {{ $settings->timezone === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                                    <option value="Europe/London" {{ $settings->timezone === 'Europe/London' ? 'selected' : '' }}>London (GMT)</option>
                                    <option value="Europe/Paris" {{ $settings->timezone === 'Europe/Paris' ? 'selected' : '' }}>Paris (CET)</option>
                                    <option value="Asia/Dubai" {{ $settings->timezone === 'Asia/Dubai' ? 'selected' : '' }}>Dubai (GST)</option>
                                    <option value="Asia/Tokyo" {{ $settings->timezone === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (JST)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="language" class="form-label">Language</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ $settings->language === 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ $settings->language === 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ $settings->language === 'de' ? 'selected' : '' }}>German</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="date_format" class="form-label">Date Format</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="Y-m-d" {{ $settings->date_format === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-12-23)</option>
                                    <option value="m/d/Y" {{ $settings->date_format === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/23/2024)</option>
                                    <option value="d/m/Y" {{ $settings->date_format === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (23/12/2024)</option>
                                    <option value="d M Y" {{ $settings->date_format === 'd M Y' ? 'selected' : '' }}>DD MMM YYYY (23 Dec 2024)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="time_format" class="form-label">Time Format</label>
                                <select class="form-select" id="time_format" name="time_format">
                                    <option value="H:i" {{ $settings->time_format === 'H:i' ? 'selected' : '' }}>24-hour (14:30)</option>
                                    <option value="h:i A" {{ $settings->time_format === 'h:i A' ? 'selected' : '' }}>12-hour (02:30 PM)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="items_per_page" class="form-label">Items Per Page</label>
                                <input type="number" class="form-control" id="items_per_page" name="items_per_page" value="{{ $settings->items_per_page }}" min="5" max="100">
                                <small class="form-text text-muted">Number of items to display per page in lists</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" {{ $settings->dark_mode ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dark_mode">
                                        <strong>Dark Mode</strong>
                                        <small class="d-block text-muted">Enable dark theme</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save General Settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Project Tab -->
        <div class="tab-pane fade" id="projectTab" role="tabpanel" aria-labelledby="project-tab">
            <form id="projectForm">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h5><i class="bi bi-folder me-2"></i>Project Preferences</h5>
                    </div>
                    <div class="settings-card-body">
                        <div class="settings-section">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="auto_assign_staff" name="auto_assign_staff" {{ $settings->auto_assign_staff ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_assign_staff">
                                    <strong>Auto-Assign Staff</strong>
                                    <small class="d-block text-muted">Automatically assign staff to new leads/clients</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="show_completed_tasks" name="show_completed_tasks" {{ $settings->show_completed_tasks ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_completed_tasks">
                                    <strong>Show Completed Tasks</strong>
                                    <small class="d-block text-muted">Display completed tasks in task lists</small>
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="show_archived_items" name="show_archived_items" {{ $settings->show_archived_items ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_archived_items">
                                    <strong>Show Archived Items</strong>
                                    <small class="d-block text-muted">Include archived items in listings</small>
                                </label>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-section-title">Reminder Settings</div>
                            <div class="mb-3">
                                <label for="default_reminder_days" class="form-label">Default Reminder Days</label>
                                <input type="number" class="form-control" id="default_reminder_days" name="default_reminder_days" value="{{ $settings->default_reminder_days }}" min="1" max="30">
                                <small class="form-text text-muted">Number of days before due date to send reminders</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Project Settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        let isSubmitting = false;

        // Notifications Form
        const notificationsForm = document.getElementById('notificationsForm');
        if (notificationsForm) {
            notificationsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;

                const formData = new FormData(this);
                // Convert checkboxes to boolean values
                const data = {};
                for (let [key, value] of formData.entries()) {
                    const checkbox = this.querySelector(`[name="${key}"][type="checkbox"]`);
                    if (checkbox) {
                        data[key] = checkbox.checked;
                    } else {
                        data[key] = value;
                    }
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                try {
                    const response = await fetch('{{ route("admin.settings.notifications.update") }}', {
                        method: 'POST',
                        body: JSON.stringify(data),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        if (typeof showToast !== 'undefined') {
                            showToast('success', data.message);
                        }
                    } else {
                        if (typeof showToast !== 'undefined') {
                            showToast('error', data.message || 'Failed to update settings');
                        }
                    }
                } catch (error) {
                    if (typeof showToast !== 'undefined') {
                        showToast('error', 'Failed to update settings: ' + error.message);
                    }
                } finally {
                    isSubmitting = false;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // Messages Form
        const messagesForm = document.getElementById('messagesForm');
        if (messagesForm) {
            messagesForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;

                const formData = new FormData(this);
                // Convert checkboxes to boolean values
                const data = {};
                for (let [key, value] of formData.entries()) {
                    const checkbox = this.querySelector(`[name="${key}"][type="checkbox"]`);
                    if (checkbox) {
                        data[key] = checkbox.checked;
                    } else {
                        data[key] = value;
                    }
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                try {
                    const response = await fetch('{{ route("admin.settings.messages.update") }}', {
                        method: 'POST',
                        body: JSON.stringify(data),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        if (typeof showToast !== 'undefined') {
                            showToast('success', data.message);
                        }
                    } else {
                        if (typeof showToast !== 'undefined') {
                            showToast('error', data.message || 'Failed to update settings');
                        }
                    }
                } catch (error) {
                    if (typeof showToast !== 'undefined') {
                        showToast('error', 'Failed to update settings: ' + error.message);
                    }
                } finally {
                    isSubmitting = false;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // General Form
        const generalForm = document.getElementById('generalForm');
        if (generalForm) {
            generalForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;

                const formData = new FormData(this);
                // Convert checkboxes to boolean values
                const data = {};
                for (let [key, value] of formData.entries()) {
                    const checkbox = this.querySelector(`[name="${key}"][type="checkbox"]`);
                    if (checkbox) {
                        data[key] = checkbox.checked;
                    } else {
                        data[key] = value;
                    }
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                try {
                    const response = await fetch('{{ route("admin.settings.general.update") }}', {
                        method: 'POST',
                        body: JSON.stringify(data),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        if (typeof showToast !== 'undefined') {
                            showToast('success', data.message);
                        }
                    } else {
                        if (typeof showToast !== 'undefined') {
                            showToast('error', data.message || 'Failed to update settings');
                        }
                    }
                } catch (error) {
                    if (typeof showToast !== 'undefined') {
                        showToast('error', 'Failed to update settings: ' + error.message);
                    }
                } finally {
                    isSubmitting = false;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // Project Form
        const projectForm = document.getElementById('projectForm');
        if (projectForm) {
            projectForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (isSubmitting) return;
                isSubmitting = true;

                const formData = new FormData(this);
                // Convert checkboxes to boolean values
                const data = {};
                for (let [key, value] of formData.entries()) {
                    const checkbox = this.querySelector(`[name="${key}"][type="checkbox"]`);
                    if (checkbox) {
                        data[key] = checkbox.checked;
                    } else {
                        data[key] = value;
                    }
                }

                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                try {
                    const response = await fetch('{{ route("admin.settings.project.update") }}', {
                        method: 'POST',
                        body: JSON.stringify(data),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        if (typeof showToast !== 'undefined') {
                            showToast('success', data.message);
                        }
                    } else {
                        if (typeof showToast !== 'undefined') {
                            showToast('error', data.message || 'Failed to update settings');
                        }
                    }
                } catch (error) {
                    if (typeof showToast !== 'undefined') {
                        showToast('error', 'Failed to update settings: ' + error.message);
                    }
                } finally {
                    isSubmitting = false;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }
    })();
</script>
@endpush
@endsection
