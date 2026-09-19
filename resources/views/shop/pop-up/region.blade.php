@php
    /** @var string|null $ip — IP-адрес клиента (передаётся через ClientComposer) */
    /** @var array|null $config — данные shop.frontend (передаются через WebComposer) */
    $regionIp = $ip ?? null;
    $regionToken = data_get($config, 'token_dadata');
@endphp

<div class="modal fade" id="region-popup" tabindex="-1" aria-labelledby="regionModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex justify-content-between p-2 text-center mb-4 align-items-center">
                <p class="modal-title fs-4" id="regionModalLabel">Выберите ваш регион</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fs-7 text-muted">От выбранного региона зависят наличие и стоимость товаров.</p>
                <select id="region-select" class="form-select" aria-label="Выбор региона">
                    <option value="">— Выберите регион —</option>
                </select>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" id="region-cancel">Отмена</button>
                <button type="button" class="btn btn-dark" id="region-ok">Ок</button>
            </div>
        </div>
    </div>
</div>

@if($regionIp && $regionToken)
<script>
    window.regionSettings = {
        ip: @json($regionIp),
        token: @json($regionToken)
    };
</script>
@endif
