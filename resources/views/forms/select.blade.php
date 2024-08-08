@if(!empty($label))
    <label for="{{ $id }}" class="form-label w-full flex flex-col sm:flex-row">{{ $label }}<span
            class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">{{ $label_description }}</span> </label>
@endif
<select id="{{ $id }}" name="{{ $name }}"
        class="form-select sm:mr-2 {{ $class }} {{ $errors->has($name) ? 'has-error' : '' }}"
        aria-label="{{ $placeholder }}" {{ $disabled }}>
    @foreach($options as $key => $value)
        <option
            value="{{ $key }}"
            @if(!empty(old($name)))
                {{ old($name)  == $key ? 'selected' : '' }}
            @else
                {{ $selected  == $key ? 'selected' : '' }}
            @endif
            >{{ $value }}
        </option>
    @endforeach
</select>
@error($name)
<div class="pristine-error text-danger mt-2">{{ $message }}</div>
@enderror
