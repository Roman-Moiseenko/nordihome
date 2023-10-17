@if(!empty($label))
    <label for="{{ $id }}" class="form-label w-full flex flex-col sm:flex-row">{{ $label }}<span
            class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">{{ $label_description }}</span> </label>
@endif
<select id="{{ $id }}" name="{{ $name }}"
        class="tom-select w-full {{ $class }} {{ $errors->has($name) ? 'has-error' : '' }}"
        data-placeholder="{{ $placeholder }}" data-header="{{ $header }}"
        {{ $disabled }} multiple>
    @foreach($options as $value)
        <option
            value="{{ $value }}"
            @if(!empty(old($name)))
                {{ old($name)  == $value ? 'selected' : '' }}
            @else
                {{ in_array($value, $selected) ? 'selected' : '' }}
            @endif
            >{{ $value }}
        </option>
    @endforeach
</select>
@error($name)
<div class="pristine-error text-danger mt-2">{{ $message }}</div>
@enderror
