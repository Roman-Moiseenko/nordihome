<select id="{{ $id }}" name="{{ $name }}" class="form-select sm:mr-2 {{ $class }}
@error($name)
    has-error
@enderror
    " aria-label="{{ $placeholder }}" {{ $disabled }}>
    @foreach($options as $key => $value)
        <option
            value="{{ $key }}"
            @if(empty(old($name)))
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
