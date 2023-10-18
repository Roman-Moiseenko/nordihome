<tr>
    <td class="w-20">IMG</td>
    <td class="w-20">ICON</td>
    <td><a href="{{ route('admin.product.category.show', $category) }}" class="font-medium">{{ $category->name }}</a></td>
    <td class="w-40">
        <div class="flex justify-center">
            @if(count($category->children) > 0)
                {{ count($category->children) }}
                <a href="" class="show-children px-1" show="hide"  target="children-{{ $category->id }}">
                    <div>
                        <i data-lucide="chevron-down" width="24" height="24" class="lucide lucide-chevron-down w-4 h-4"></i>
                    </div>
                </a>
            @endif
        </div>

    </td>
    <td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.category.edit', $category) }}">
                <i data-lucide="check-square" class="w-4 h-4"></i> Edit </a>
            <a class="flex items-center mr-3 text-primary" href="{{ route('admin.product.category.store', $category) }}">
                <i data-lucide="plus-square" class="w-4 h-4"></i> Add</a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.category.destroy', $category) }}>
                <i data-lucide="trash-2" class="w-4 h-4"></i> Delete </a>

        </div>
    </td>
</tr>
@if(count($category->children) != 0)
    <tr id="children-{{ $category->id }}" class="hidden" style="background-color: #e5e7eb !important;">
        <td colspan="5">
            <table class="table table-report -mt-2">
                <tbody>
                    @foreach($category->children as $children)
                        @include('admin.product.category._list', ['category' => $children])
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endif


