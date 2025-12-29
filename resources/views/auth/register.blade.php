@extends('layouts.guest')

@section('title', 'Register')

@push('styles')
    @vite(['resources/css/auth.css'])
@endpush

@push('scripts')
    @vite(['resources/js/auth.js'])
@endpush

@section('content')
@php
    $roles = [
        [
            'value' => 'client',
            'icon' => 'bi-building',
            'title' => 'Client',
            'subtitle' => 'For Businesses & Companies',
            'features' => ['Manage facilities', 'Track services', 'View reports']
        ],
        [
            'value' => 'staff',
            'icon' => 'bi-person-badge',
            'title' => 'Staff',
            'subtitle' => 'For Employees & Workers',
            'features' => ['Log timesheets', 'Upload photos', 'Track activities']
        ]
    ];

    $sidebarFeatures = [
        ['icon' => 'bi-building', 'label' => 'Facilities'],
        ['icon' => 'bi-graph-up', 'label' => 'Analytics'],
        ['icon' => 'bi-gear', 'label' => 'Management']
    ];

    $formFields = [
        [
            'type' => 'text',
            'name' => 'name',
            'label' => 'Full Name',
            'icon' => 'bi-person',
            'placeholder' => 'Enter your full name',
            'required' => true,
            'autofocus' => true,
            'autocomplete' => 'name'
        ],
        [
            'type' => 'email',
            'name' => 'email',
            'label' => 'Email Address',
            'icon' => 'bi-envelope',
            'placeholder' => 'Enter your email',
            'required' => true,
            'autocomplete' => 'email'
        ],
        [
            'type' => 'password',
            'name' => 'password',
            'label' => 'Password',
            'icon' => 'bi-lock',
            'placeholder' => 'Create a password',
            'required' => true,
            'autocomplete' => 'new-password',
            'showToggle' => true
        ],
        [
            'type' => 'password',
            'name' => 'password_confirmation',
            'label' => 'Confirm Password',
            'icon' => 'bi-lock-fill',
            'placeholder' => 'Confirm your password',
            'required' => true,
            'autocomplete' => 'new-password',
            'showToggle' => true
        ]
    ];

    $clientFields = [
        [
            'type' => 'text',
            'name' => 'company_name',
            'label' => 'Company Name',
            'icon' => 'bi-building',
            'placeholder' => 'Enter company name',
            'required' => false
        ]
    ];
@endphp

<x-auth-page-wrapper
    page-class="login-page"
    :show-sidebar="true"
    sidebar-title="Join Our Platform!"
    sidebar-subtitle="Create your account and start managing your facilities with our comprehensive ERP solution"
    sidebar-image="office-cleaning.jpg"
    :sidebar-features="$sidebarFeatures"
>
    <x-role-selection
        title="Create Account"
        subtitle="Choose your account type to get started"
        :roles="$roles"
        :selected-role="old('role')"
        :error="$errors->any() ? $errors->first() : null"
        :show-login-link="true"
        login-route="login"
        login-text="Already have an account?"
        login-link-text="Login here"
    />

    <x-registration-form
        :action="route('register')"
        method="POST"
        title="Complete Registration"
        subtitle="Fill in your details to create your account"
        :show-back-button="true"
        back-button-id="backToRole"
        form-id="registerForm"
        submit-text="Create Account"
        submit-icon="bi-person-plus"
        :fields="$formFields"
        :client-fields="$clientFields"
    />
</x-auth-page-wrapper>
@endsection
