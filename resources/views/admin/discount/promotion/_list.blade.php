<tr class="intro-x">
    <td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $promotion->getImage() }}" alt="{{ $promotion->name }}">
        </div>
    </td>
    <td class="w-40"><a href="{{ route('admin.discount.promotion.show', $promotion) }}"
                        class="font-medium whitespace-nowrap">{{ $promotion->name }}</a></td>
    <td class="text-center">{{ is_null($promotion->start_at) ? 'в ручном режиме' :$promotion->start_at->translatedFormat('j F Y') }} - {{ $promotion->finish_at->translatedFormat('j F Y') }}</td>
    <td class="text-center">{!! App\Helpers\PromotionHelper::html($promotion) !!}</td>
    <td class="text-center">{{ $promotion->countProducts() }}</td>

    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($promotion->status() == \App\Modules\Discount\Entity\Promotion::STATUS_DRAFT)
            <a class="flex items-center mr-3" href="{{ route('admin.discount.promotion.edit', $promotion) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.discount.promotion.destroy', $promotion) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
            @endif
        </div>
    </td>
</tr>
