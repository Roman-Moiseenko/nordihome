<div class="button-contacts">
    <button id="menu-contacts" ><i class="fa-light fa-align-center"></i></button>
    <div id="list-contacts" class="list hidden">
        @foreach(\App\Modules\NBRussia\Helper\MenuHelper::getMenuContacts() as $item)
            <div class="ms-2">
                <a href="{{ $item['url'] }}" target="_blank" title="{{ $item['name'] }}">
                    <i class="{{ $item['icon'] }}" style="color: {{ $item['color'] }}"></i>
                </a>
            </div>
        @endforeach
    </div>
</div>

<script>
    let buttonMenuContacts = document.getElementById('menu-contacts');
    let divListContacts = document.getElementById('list-contacts');
    buttonMenuContacts.addEventListener('click', function ()  {
        divListContacts.classList.toggle('hidden');
    })
</script>
