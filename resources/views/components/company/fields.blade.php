<div>
    <span id="dadata" data-url="{{ env('DADATA_FIND_PARTY_URL') }}" data-token="{{ env('DADATA_TOKEN') }}"
          data-bank="{{ env('DADATA_FIND_BANK_URL') }}"></span>
    <x-company.fieldDetail :company="$company" />
    <x-company.fieldAddress :legal_address="$legal_address" :actual_address="$actual_address"/>
    <x-company.fieldBank :company="$company" />
    <x-company.fieldContact :company="$company" />
    @once
        @push('scripts')
            @vite('resources/js/components/company/fields.js')
        @endpush
    @endonce
</div>
