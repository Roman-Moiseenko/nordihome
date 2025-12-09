## CRM Nordi Home

Будет текст ......

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

...... 


# Описание внутреннего API
## E-Commerce

Принцип работы, элементы DOM должны содержать соответствующий класс учета e-commerce и атрибут **data-product** с id товара

### 1. impressions - просмотр списка товаров

Элементы каталога (карточка товара) должны содержать класс [e-impressions]() и тег [data-product]() с id товара

**Например**

    <div class="product-card e-impressions" data-product="{{ $product['id'] }}">

    ... здесь карточка Товара ...

    </div>

### 2. click - клик по товару в списке

Каждый тег \<a> отвечающий за переход на карточку товара, должен содержать класс [e-click]() и тег [data-product]() с id товара

    <a class="e-click" data-product="{{ $product['id'] }}" href="{{ route('shop.product.view', $product['slug']) }}">

    текст или изображение

    </a>
Встречается 3 раза.

### 3. detail - просмотр товара
Подключение происходит в любом месте карточки товара через любой невидимый тег, например:
    
    <span class="e-detail" data-product="{{ $product['id'] }}"></span>

Срабатывает при скроллинге экрана

### 4. add - добавление товара в корзину
Для отправки в корзину параллельно с классом to-cart необходимо использовать e-add. На текущий момент в корзину можно отправить товар только из карточки товара, для отправки из списка, необходимо повторить код кнопки
    
    <button class="to-cart e-add" data-product="{{ $product['id'] }}">В Корзину</button>

### 5. remove - удаление товара из корзины
Удаление товаров из корзины производится в компоненте Livewire: livewire.shop.header.cart
Если удаление, очистка и покупка используется без **livewire**, то использовать следующий код:
В случае **livewire** компонент сам вызывает нужные события
Принцип учета в e-commerce такой же, только добавляется кол-во (см. пример), для тега \<a> прописываем:

    <a class="e-remove" 
        data-product="{{ $item['product_id'] }}" 
        data-quantity="{{ $item['quantity'] }}" ... />    

_add и remove_ используются в двух местах: виджет корзина в header и на странице Корзина в кабинете пользователя. Оба случая - компоненты **livewire**

#### 5.1. clear - очистка корзины
При очистке корзины надо списком передать все id, для этого используем вспомогательное событие e-clear
тег data-product содержит массив id товаров

    <button class="e-clear"  
        data-product="[@foreach($items as $item){{$item['product_id']}},@endforeach 0]"
        wire:click="clear_cart">
        Очистить корзину
    </button>

### 6. purchase - покупка
Аналогично предыдущему подпункту, необходимо указать перечень всех id товаров и использовать класс **e-purchase** 

## Купить в 1 клик 
Необходимо разместить следующую кнопку, передав ей id товара
Кнопка открывает модальное окно (через bootstrap) и передает компоненту Livewire id товара

    <button class="btn btn-outline-dark"
        type="button" data-bs-toggle="modal"
        data-bs-target="#buy-click"
        onclick="Livewire.dispatch('buy-click', {id: {{$product['id']}} });"
    >В 1 Клик!
    </button>

Текущий вариант, без Livewire

    <button class="one-click btn btn-outline-dark"
        type="button" data-bs-toggle="modal"
        data-bs-target="#buy-click"
        onclick="document.getElementById('one-click-product-id').value={{$product['id']}};
        document.getElementById('button-buy-click').setAttribute('data-product', {{$product['id']}});"
    >В 1 Клик!
    </button>

# Меню

Меню создается в админке в разделе Фронтенд

В коде шаблона - в хедере и в футере ищем по слагу нужное меню в глобальной перменной **$menus**: 

    <div>{{ $menus['footer'] }}</div>
    <ul class="menu">
    @foreach($menus['footer']['items'] as $item)
        <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
    @endforeach
    </ul>
