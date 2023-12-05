<div class="form-check form-switch {{ $class }}">
    <input id="{{ $id }}" class="form-check-input" type="checkbox" name="{{ $name }}"
        {{ (bool)$value ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
    <label class="form-check-label" for="{{ $id }}">{{ $placeholder }} </label>
</div>


