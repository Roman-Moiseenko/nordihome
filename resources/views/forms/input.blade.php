<div class="{{ !empty($group) ? 'input-group' : 'input-form' }} {{ $class }} {{ $errors->has($name) ? 'has-error' : '' }}">
    @if(!empty($label))
        <label for="{{ $id }}" class="form-label w-full flex flex-col sm:flex-row">{{ $label }}<span
                class="sm:ml-auto mt-1 sm:mt-0 text-xs text-slate-500">{{ $label_description }}</span> </label>
    @endif
    @if(!empty($group))
        <div id="input-group-{{ $name }}" class="input-group-text">{!! $group !!}</div>
    @endif

    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" class="form-control "
           placeholder="{{ $placeholder }}" @if(!empty($group)) aria-describedby="input-group-{{ $name }}" @endif
           value="{{ old($name) ?? $value }}" {{ $disabled }}>
    @if(empty($group))
        @error($name)
        <div class="pristine-error text-danger mt-2">{{ $message }}</div>
        @enderror
    @endif

</div>
@if(!empty($group))
    @error($name)
    <div class="pristine-error text-danger mt-2">{{ $message }}</div>
    @enderror
@endif
