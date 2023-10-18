<tr>
    <td class="w-20"><div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $category->getImage() }}" alt="{{ $category->name }}"></div></td>
    <td class="w-20"><div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $category->getIcon() }}" alt="{{ $category->name }}"></div></td>
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
    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.category.up', $category) }}"
               onclick="event.preventDefault(); document.getElementById('category-up-{{ $category->id }}').submit();">
                <i data-lucide="arrow-up" class="w-4 h-4"></i></a>
                <form id="category-up-{{ $category->id }}" action="{{ route('admin.product.category.up', $category) }}" method="POST" class="hidden">
                    @csrf
                </form>
            <a class="flex items-center mr-3" href="{{ route('admin.product.category.down', $category) }}"
               onclick="event.preventDefault(); document.getElementById('category-down-{{ $category->id }}').submit();">
                <i data-lucide="arrow-down" class="w-4 h-4"></i></a>
                <form id="category-down-{{ $category->id }}" action="{{ route('admin.product.category.up', $category) }}" method="POST" class="hidden">
                    @csrf
                </form>

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
        <td colspan="5" style="padding: 0;">
            <div class="px-4 bg-slate-100">
            <table class="table table-report -mt-2">
                <tbody>
                    @foreach($category->children as $children)
                        @include('admin.product.category._list', ['category' => $children])
                    @endforeach
                </tbody>
            </table>
            </div>
        </td>
    </tr>
@endif


