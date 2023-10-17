@if(!empty($label))
    <label for="{{ $id }}" class="form-label w-full flex flex-col sm:flex-row">{{ $label }}<span
            class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">{{ $label_description }}</span> </label>
@endif
<textarea id="{{ $id }}" name="{{ $name }}"
          class="form-control sm:mr-2 {{ $class }} {{ $errors->has($name) ? 'has-error' : '' }}"
          rows="{{ $rows }}" placeholder="{{ $placeholder }}" {{ $disabled }}>{{ old($name) ?? $value }}</textarea>

@error($name)
<div class="pristine-error text-danger mt-2">{{ $message }}</div>
@enderror

