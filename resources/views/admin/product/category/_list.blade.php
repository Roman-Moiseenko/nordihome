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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down w-4 h-4"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </a>
            @endif
        </div>

    </td>
    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-1" href="{{ route('admin.product.category.up', $category) }}" title="up"
               onclick="event.preventDefault(); document.getElementById('category-up-{{ $category->id }}').submit();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up w-4 h-4"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>
            </a>
                <form id="category-up-{{ $category->id }}" action="{{ route('admin.product.category.up', $category) }}" method="POST" class="hidden">
                    @csrf
                </form>
            <a class="flex items-center mr-4" href="{{ route('admin.product.category.down', $category) }}" title="down"
               onclick="event.preventDefault(); document.getElementById('category-down-{{ $category->id }}').submit();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down w-4 h-4"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
            </a>
                <form id="category-down-{{ $category->id }}" action="{{ route('admin.product.category.up', $category) }}" method="POST" class="hidden">
                    @csrf
                </form>

            <a class="flex items-center mr-3" href="{{ route('admin.product.category.edit', $category) }}" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-square w-4 h-4"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Edit </a>
            <a class="flex items-center mr-3 text-primary" href="{{ route('admin.product.category.store', $category) }}" title="Add">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-square w-4 h-4"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                Add</a>
            <a class="flex items-center text-danger" href="#" title="Delete"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.category.destroy', $category) }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                Delete </a>

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


