<!--template:Главная - Подробности доставки в другие города-->
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
    /** @var \App\Modules\Page\Entity\Widgets\TextWidget $widget */
@endphp
<div class="p-t_50 p-b_50 bg-blue f-z_23 t-a_center t-t_uppercase f-w_600">
    @foreach($widget->items as $item)
        <div class="container-fluid">
            {{$item->caption}}<br><a href="/usloviya-i-tarify/"
                                     class="btn btn-white t-t_uppercase f-z_14 m-t_20">{{$item->description}}</a>
        </div>
    @endforeach
</div>
