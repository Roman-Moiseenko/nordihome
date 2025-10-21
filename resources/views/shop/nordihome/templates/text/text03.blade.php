<!--template:Почему нас выбираю для главной-->
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
<div class="main-advantages p-t_50 p-b_50">
    <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
    <div class="row">
        @foreach($widget->items as $item)
            <div class="col-md-6 col-lg-6">
                <div class="item-advantages t-a_center m-b_30">
                    <div>
                        @if($item->slug=='svg01-item-advantages')
                            <svg fill="#ffffff" height="100px" width="100px" version="1.1" id="Layer_1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink" viewBox="-92.45 -92.45 449.05 449.05" xml:space="preserve" stroke="#ffffff" stroke-width="0.0026414800000000003">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0">
                                        <rect x="-92.45" y="-92.45" width="449.05" height="449.05" rx="224.525" fill="#0088cc" strokewidth="0"></rect>
                                    </g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier"> <g> <g> <g> <path d="M52.14,55.775h134.611c4.142,0,7.5-3.358,7.5-7.5c0-4.142-3.358-7.5-7.5-7.5H52.14c-4.142,0-7.5,3.358-7.5,7.5 C44.64,52.417,47.998,55.775,52.14,55.775z"></path> <path d="M114.996,166.474H52.14c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.358,7.5,7.5,7.5h62.856c4.142,0,7.5-3.358,7.5-7.5 C122.496,169.832,119.138,166.474,114.996,166.474z"></path> <path d="M114.996,208.373H52.14c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.358,7.5,7.5,7.5h62.856c4.142,0,7.5-3.358,7.5-7.5 C122.496,211.731,119.138,208.373,114.996,208.373z"></path> <path d="M221.867,54.974V25.296C221.867,11.348,210.519,0,196.571,0H42.32C28.372,0,17.024,11.348,17.024,25.296v213.556 c0,13.948,11.348,25.296,25.296,25.296h154.251c13.948,0,25.296-11.348,25.296-25.296v-86.13l23.775-65.321 C251.554,71.154,238.998,54.206,221.867,54.974z M206.868,238.852h-0.001c0,5.677-4.619,10.296-10.296,10.296H42.32 c-5.677,0-10.296-4.619-10.296-10.296V25.296C32.024,19.619,36.643,15,42.32,15h154.251c5.677,0,10.296,4.619,10.296,10.296 v35.751c-5.955,5.267-6.657,9.764-12.929,26.994c-0.92-3.102-3.787-5.367-7.188-5.367H52.14c-4.142,0-7.5,3.358-7.5,7.5 c0,4.142,3.358,7.5,7.5,7.5h134.611c1.529,0,2.949-0.46,4.135-1.245l-10.244,28.145H52.14c-4.142,0-7.5,3.358-7.5,7.5 c0,4.142,3.358,7.5,7.5,7.5h123.042l-16.301,44.788c-0.511,1.404-0.591,2.929-0.229,4.379l7.218,28.946 c1.286,5.155,7.393,7.37,11.686,4.253l24.136-17.535c2.375-1.726,2.578-3.332,5.176-10.472V238.852z M231.546,82.271 l-24.227,66.563v0.001l-16.3,44.783l-13.286,9.652l-3.973-15.934L214.288,75.99c1.725-4.742,7.007-7.222,11.77-5.489 C230.827,72.236,233.283,77.501,231.546,82.271z"></path> </g> </g> </g> </g>
                            </svg>
                        @endif
                    </div>
                    <div class="item-advantages-info">
                        <div class="heading">{{ $item->caption }}</div>
                        <div class="text">{{$item->description}}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
