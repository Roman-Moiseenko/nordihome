<livewire:admin.sales.order.user-info :order="$order" />

<livewire:admin.sales.order.manager-info :order="$order" />

@include('admin.sales.order.manager.__actions', ['order' => $order])
