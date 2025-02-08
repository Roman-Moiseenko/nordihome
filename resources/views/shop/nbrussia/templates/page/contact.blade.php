<!--template:Контакты-->
@extends('shop.nbrussia.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')

    <div class="container-xl">
        <h1>{{ $page->name }}</h1>
        <div class="row mt-4">
            <div class="col-lg-6 ps-md-4" style="display: grid">
                <div class="about-block">
                    <div class="heading-border">
                        О КОМПАНИИ
                    </div>
                    <p>
                        ООО "Кёнигс.РУ" занимается поставками одежды и продукции легкой промышленности известных брендов из европы, в том числе из Польши.
                        Бренд NB Russia - это бренд New Balance в России.

                    </p>
                    <p>тел. <a href="tel:+7" style="color: var(--bs-secondary-700);">+7(9..) ... ....</a></p>
                    <p>ООО «Кёнигс.ру», ИНН 3906396773, КПП 390601001, <span>236001</span>
                        <span>Калининград</span></p>
                </div>

            </div>
            <div class="col-lg-6 pe-md-4">
                ***
            </div>
        </div>
    </div>

    <livewire:shop.widget.feedback />

@endsection
