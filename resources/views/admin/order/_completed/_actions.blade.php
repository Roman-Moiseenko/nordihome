<h2 class=" mt-3 font-medium">Действия</h2>
<div class="box flex p-3 lg:justify-start buttons-block items-start">
    <button class="btn btn-warning-soft" onclick="document.getElementById('form-order-copy').submit();">Скопировать</button>
    <form id="form-order-copy" method="post" action="{{ route('admin.order.copy', $order) }}">
        @csrf
    </form>
</div>
