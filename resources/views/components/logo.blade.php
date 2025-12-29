@props([
    'height' => 45,
    'class' => '',
    'alt' => 'Keystone Logo'
])

@if(file_exists(public_path('logo.png')))
    <img src="{{ asset('logo.png') }}" alt="{{ $alt }}" height="{{ $height }}" class="{{ $class }}" onerror="this.src='/Image-not-found.png'; this.onerror=null;">
@endif

