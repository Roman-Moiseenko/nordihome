<button class="btn btn-warning-soft" onclick="document.getElementById('form-order-copy').submit();">Скопировать</button>
<form id="form-order-copy" method="post" action="{{ route('admin.order.copy', $order) }}">
    @csrf
</form>

