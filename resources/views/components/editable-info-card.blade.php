@props([
    'label',
    'value' => null,
    'badge' => null,
    'badgeColor' => 'primary',
    'link' => null,
    'field' => null,
    'entityType' => null,
    'entityId' => null,
    'fieldType' => 'text',
    'options' => [],
    'editable' => true
])

@php
    $isEditable = $editable && $field && $entityType && $entityId;
    $displayValue = $value ?? ($badge ? $badge : '-');
@endphp

<div class="info-card editable-info-card" data-field="{{ $field }}" data-entity-type="{{ $entityType }}" data-entity-id="{{ $entityId }}" data-field-type="{{ $fieldType }}">
    <div class="info-label">
        {{ $label }}
        @if($isEditable)
            <button class="btn-edit-field btn btn-sm btn-link p-0 ms-2" title="Edit">
                <i class="bi bi-pencil text-muted"></i>
            </button>
        @endif
    </div>
    <div class="info-value">
        @if($isEditable)
            <span class="field-display">{{ $displayValue }}</span>
            @if($fieldType === 'select')
                <select class="form-control form-control-sm field-edit d-none" style="max-width: 200px;">
                    <option value="">Select {{ $label }}</option>
                    @foreach($options as $key => $option)
                        <option value="{{ $key }}" {{ $value == $key ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            @elseif($fieldType === 'textarea')
                <textarea class="form-control field-edit d-none" rows="3">{{ $value }}</textarea>
            @else
                <input type="{{ $fieldType }}" class="form-control form-control-sm field-edit d-none" value="{{ $fieldType === 'number' && strpos($value, '$') !== false ? str_replace(['$', ','], '', $value) : $value }}" placeholder="Enter {{ strtolower($label) }}" step="{{ $fieldType === 'number' ? '0.01' : '' }}">
            @endif
            <div class="field-actions d-none mt-2">
                <button class="btn btn-sm btn-success btn-save-field me-1">
                    <i class="bi bi-check"></i> Save
                </button>
                <button class="btn btn-sm btn-secondary btn-cancel-field">
                    <i class="bi bi-x"></i> Cancel
                </button>
            </div>
        @else
            @if($link)
                <a href="{{ $link }}" class="text-decoration-none">{{ $displayValue }}</a>
            @elseif($badge)
                <span class="badge bg-{{ $badgeColor }}">{{ $badge }}</span>
            @else
                {{ $displayValue }}
            @endif
        @endif
    </div>
</div>
