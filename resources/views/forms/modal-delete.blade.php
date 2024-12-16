<x-base.dialog id="{{ $id }}">
    <x-base.dialog.panel>
        <div class="p-5 text-center">

            <div class="mt-5 text-3xl">{{ $caption }}</div>
            <div class="mt-2 text-slate-500">
                {!! $text !!}
            </div>
        </div>
        <div class="px-5 pb-8 text-center">
            <x-base.button class="mr-1 w-24" data-tw-dismiss="modal" type="button" variant="outline-secondary">
                Отмена
            </x-base.button>
            <x-base.button class="w-24" type="button" variant="danger"
                onclick="event.preventDefault(); document.getElementById('modal-destroy-form').submit();">
                Удалить
            </x-base.button>
            <form id="modal-destroy-form" action="/" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </x-base.dialog.panel>
</x-base.dialog>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let FullList = document.getElementsByTagName('body')[0];
        FullList.addEventListener('click', function (e) {
            if (e.target.nodeName === 'A' || e.target.nodeName === 'BUTTON') {
                if (e.target.getAttribute('data-tw-toggle') === 'modal') {
                    e.preventDefault();
                    let route = e.target.getAttribute('data-route');
                    let form = document.getElementById('modal-destroy-form');
                    form.setAttribute('action', route);
                }
            }
        });
    });
</script>
