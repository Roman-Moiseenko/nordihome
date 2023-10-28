<tr>
    <td class="w-20"><div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $category->getImage() }}" alt="{{ $category->name }}"></div></td>
    <td class="w-20"><div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $category->getIcon() }}" alt="{{ $category->name }}"></div></td>
    <td><a href="{{ route('admin.product.category.show', $category) }}" class="font-medium">{{ $category->name }}</a></td>

    <td><span class="text-slate-500 flex items-center"><x-base.lucide icon="external-link" class="w-4 h-4"/> {{ $category->getSlug() }}</span></td>

    <td class="w-40">
        <div class="flex justify-center">
            @if(count($category->children) > 0)
                {{ count($category->children) }}
                <a href="" class="show-children px-1" show="hide"  target="children-{{ $category->id }}">
                    <div>
                        <x-base.lucide icon="chevron-down" class="w-4 h-4"/>
                    </div>
                </a>
            @endif
        </div>
    </td>
    <td class="table-report__action w-72">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-1" href="{{ route('admin.product.category.up', $category) }}" title="up"
               onclick="event.preventDefault(); document.getElementById('category-up-{{ $category->id }}').submit();">
                <x-base.lucide icon="arrow-up" class="w-4 h-4"/>
            </a>
                <form id="category-up-{{ $category->id }}" action="{{ route('admin.product.category.up', $category) }}" method="POST" class="hidden">
                    @csrf
                </form>
            <a class="flex items-center mr-4" href="{{ route('admin.product.category.down', $category) }}" title="down"
               onclick="event.preventDefault(); document.getElementById('category-down-{{ $category->id }}').submit();">
                <x-base.lucide icon="arrow-down" class="w-4 h-4"/>
            </a>
                <form id="category-down-{{ $category->id }}" action="{{ route('admin.product.category.down', $category) }}" method="POST" class="hidden">
                    @csrf
                </form>

            <a class="flex items-center mr-3" href="{{ route('admin.product.category.edit', $category) }}" title="Edit">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center mr-3 text-primary" href="{{ route('admin.product.category.child', $category) }}" title="Add">
                <x-base.lucide icon="plus-square" class="w-4 h-4"/>
                Add</a>
            <a class="flex items-center text-danger" href="#" title="Delete"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.category.destroy', $category) }}>
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </td>
</tr>
@if(count($category->children) != 0)
    <tr id="children-{{ $category->id }}" class="hidden" style="background-color: #e5e7eb !important;">
        <td colspan="6" style="padding: 0;">
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


