<div id="discount-widget" class="flex">
    <div class="input-form mt-3 w-full ">
        <label for="input-_from" class="form-label w-full flex flex-col sm:flex-row">Каждый месяц с:</label>
        <select id="input-_from" name="_from" class="form-select">
            @for($i = 1; $i <= 31; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="input-form mt-3 ml-3 w-full ">
        <label for="input-_to" class="form-label w-full flex flex-col sm:flex-row">по:</label>
        <select id="input-_to" name="_to" class="form-select">
            @for($i = 1; $i <= 31; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
    </div>
</div>
