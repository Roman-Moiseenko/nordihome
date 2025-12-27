<!--template:Главная - полезные статьи-->
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
<div class="main-articles p-t_50 p-b_50">
    <div class="container">
        <h2 class="page-h2">Полезные статьи</h2>
        <div class="row">
            @foreach($widget->getPost() as $post)
            <div class="col-sm-6 col-md-4">
                <a href="{{ route('shop.post.view', $post->slug) }}" class="item-article d-block">
                    <div class="img"><img src="{{ $post->getImage('post') }}" alt="{{ $post->name }}"></div>
                    <div class="m-t_10 m-b_10">{{ $post->name }}</div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="t-a_center m-t_30"><a href="/posts/poleznye-stati" class="btn btn-white-b">Посмотреть все статьи</a></div>
    </div>
</div>


