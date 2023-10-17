<div id="{{ $id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                    <div class="text-3xl mt-5">{{ $caption }}</div>
                    <div class="text-slate-500 mt-2">
                        {!! $text !!}
                    </div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Отмена</button>
                    <button type="button" class="btn btn-danger w-24"
                            onclick="event.preventDefault(); document.getElementById('modal-destroy-form').submit();">Удалить</button>
                    <form id="modal-destroy-form" action="/" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let FullList = document.getElementsByTagName('body')[0];
        FullList.addEventListener('click', function (e) {
            if (e.target.nodeName === 'A') {
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
