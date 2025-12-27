<!--template:Записи базовый-->
@php
/**
* $widget->name
* $widget->url
* $widget->caption
* $widget->description
* $widget->fields
* $widget->lists
* $widget->getPost(<кол-во последних записей>) - возвращает массив записей Post::class
    * $post->category - рубрика
    * остальные поля, например название рубрики $post->category->name и т.п.
    */

    /** @var App\Modules\Page\Entity\Widgets\PostWidget $widget  */
    /** @var \App\Modules\Page\Entity\Post $post */
    @endphp
    <div class="text-center mt-5 py-4 widget-home-3-group">
        @foreach($widget->getPost() as $post)
        <div>
            {!! $post->getParagraphs() !!}
        </div>
        @endforeach
    </div>
