<!--template:Главная - часто задаваемые вопросы-->
@php
    /**
    * TextWidget::class - string
    * $widget->caption - string
    * $widget->description - string
    * $widget->image - Photo::class
    * $widget->icon - Photo::class
    * TextWidgetItem:class
    * $widget->items - Arraible
    * $widget->itemBySlug(string)?: TextWidgetItem
    * $item->caption -
    * $item->description -
    * $item->text - text (форматируемый текст)

    */
    /** @var \App\Modules\Page\Entity\TextWidget $widget */
@endphp
<div class="block-faq p-t_50 p-b_50 bg-black" id="faq-tab">
    <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
    <div class="accordion accordion_1">
        @foreach($widget->items as $item)
            <div class="accordion-item">
                <div class="accordion-heading">{{ $item->caption }}</div>
                <div class="accordion-text">
                    <p>{!! $item->text !!}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="accordion" id="accordion-faq">
    <div class="accordion-item">
        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                Аккордеонный элемент #1
            </button>
        </h2>
        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show">
            <div class="accordion-body">
                <strong>Это тело аккордеона первого элемента.</strong> Оно отображается по умолчанию, пока плагин свертывания не добавит соответствующие классы, которые мы используем для стилизации каждого элемента. Эти классы управляют общим внешним видом, а также отображением и скрытием с помощью переходов CSS. Вы можете изменить все это с помощью собственного CSS или переопределить наши переменные по умолчанию. Также стоит отметить, что практически любой HTML может быть помещен в <code>.accordion-body</code>, хотя переход ограничивает переполнение.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <div class="accordion-header" id="panelsStayOpen-headingTwo">
            <div class="accordion-button collapsed" type="button" data-bs-toggle="collapse"  aria-expanded="false">
                Аккордеонный элемент #2
            </div>
        </div>
        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
            <div class="accordion-body">
                <strong>Это тело аккордеона второго элемента.</strong> По умолчанию он скрыт, пока плагин свертывания не добавит соответствующие классы, которые мы используем для стилизации каждого элемента. Эти классы управляют общим внешним видом, а также отображением и скрытием с помощью переходов CSS. Вы можете изменить все это с помощью собственного CSS или переопределить наши переменные по умолчанию. Также стоит отметить, что практически любой HTML может быть помещен в <code>.accordion-body</code>, хотя переход ограничивает переполнение.
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <div class="accordion-header" id="panelsStayOpen-headingThree">
            <div class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                Аккордеонный элемент #3
            </div>
        </div>
        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
            <div class="accordion-body">
                <strong>Это тело аккордеона третьего элемента.</strong> По умолчанию он скрыт, пока плагин свертывания не добавит соответствующие классы, которые мы используем для стилизации каждого элемента. Эти классы управляют общим внешним видом, а также отображением и скрытием с помощью переходов CSS. Вы можете изменить все это с помощью собственного CSS или переопределить наши переменные по умолчанию. Также стоит отметить, что практически любой HTML может быть помещен в <code>.accordion-body</code>, хотя переход ограничивает переполнение.
            </div>
        </div>
    </div>
</div>
