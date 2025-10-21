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
                        @if($item->slug=='svg02-item-advantages')
                            <svg fill="#ffffff" width="100px" height="100px" viewBox="-11.2 -11.2 54.40 54.40" xmlns="https://www.w3.org/2000/svg" stroke="#ffffff" stroke-width="0.00032">
                                <g id="SVGRepo_bgCarrier" stroke-width="0">
                                        <rect x="-11.2" y="-11.2" width="54.40" height="54.40" rx="27.2" fill="#0088cc" strokewidth="0"></rect>
                                    </g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier"> <title></title> <g data-name="Layer 2" id="Layer_2"> <path d="M30,18H24a1,1,0,0,0-1,1v1H9V19a1,1,0,0,0-1-1H2a1,1,0,0,0-1,1V30a1,1,0,0,0,1,1H30a1,1,0,0,0,1-1V19A1,1,0,0,0,30,18Zm-7,4v4H9V22ZM3,20H7v9H3Zm6,8H23v1H9Zm20,1H25V20h4Z"></path> <path d="M28,17a1,1,0,0,0,1-1V4a1,1,0,0,0-1-1H4.13a1,1,0,0,0-1,1V16a1,1,0,0,0,1,1ZM5.13,5H27V15H5.13Z"></path> <rect height="2" width="4" x="14" y="23"></rect> </g> </g>
                            </svg>
                        @endif
                        @if($item->slug=='svg03-item-advantages')
                                <svg width="100px" height="100px" viewBox="-16.8 -16.8 81.60 81.60" xmlns="https://www.w3.org/2000/svg" fill="#ffffff" stroke="#ffffff" stroke-width="0.00048000000000000007">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0">
                                        <rect x="-16.8" y="-16.8" width="81.60" height="81.60" rx="40.8" fill="#0088cc" strokewidth="0"></rect>
                                    </g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier"> <g id="Layer_2" data-name="Layer 2"> <g id="invisible_box" data-name="invisible box"> <rect width="48" height="48" fill="none"></rect> </g> <g id="Health_Icons" data-name="Health Icons"> <g> <path d="M37.7,11.1A3,3,0,0,0,35.4,10H34.2l.3-1.7A3.1,3.1,0,0,0,33.9,6a3.2,3.2,0,0,0-2.2-1H7.8a2,2,0,0,0,0,4H30.3l-4,22.9a6.8,6.8,0,0,0-1,2.1H20.7A7,7,0,0,0,7.3,34H6.2l.5-2.9a2,2,0,0,0-1.6-2.3,2,2,0,0,0-2.3,1.6L2,34.7A2.8,2.8,0,0,0,2.7,37a2.8,2.8,0,0,0,2.1,1H7.3a7,7,0,0,0,13.4,0h4.6a7,7,0,0,0,13.4,0h2a3.2,3.2,0,0,0,3.1-2.7L46,22.5ZM14,39a3,3,0,0,1-3-3,3,3,0,0,1,6,0A3,3,0,0,1,14,39ZM33.5,14h1.3l5.9,8H32.1ZM32,39a3,3,0,0,1-3-3,3,3,0,0,1,6,0A3,3,0,0,1,32,39Zm8-5H38.7A7,7,0,0,0,32,29H30.9l.5-3.1h9.9Z"></path> <path d="M4,15H14a2,2,0,0,0,0-4H4a2,2,0,0,0,0,4Z"></path> <path d="M15,19a2,2,0,0,0-2-2H5a2,2,0,0,0,0,4h8A2,2,0,0,0,15,19Z"></path> <path d="M6,23a2,2,0,0,0,0,4h6a2,2,0,0,0,0-4Z"></path> </g> </g> </g> </g>
                                </svg>
                        @endif
                        @if($item->slug=='svg04-item-advantages')
                                <svg width="100px" height="100px" viewBox="-5.6 -5.6 27.20 27.20" xmlns="https://www.w3.org/2000/svg" fill="#ffffff" stroke="#ffffff" stroke-width="0.00016">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0">
                                        <rect x="-5.6" y="-5.6" width="27.20" height="27.20" rx="13.6" fill="#0088cc" strokewidth="0"></rect>
                                    </g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier"> <path fill="#ffffff" fill-rule="evenodd" d="M10.2361,6 C10.7111,5.46924 11,4.76835 11,4 C11,2.34315 9.65685,1 8,1 C6.34315,1 5,2.34315 5,4 C5,4.76835 5.28885,5.46924 5.76389,6 L3.00003,6 L1.26411,12.4827 C0.923988,13.7528 1.88112,15 3.19604,15 L12.8013,15 C14.1159,15 15.073,13.7534 14.7334,12.4834 L13,6 L10.2361,6 Z M9,4 C9,4.55228 8.55228,5 8,5 C7.44772,5 7,4.55228 7,4 C7,3.44772 7.44772,3 8,3 C8.55228,3 9,3.44772 9,4 Z M3.19604,13 L4.53493,8 L11.4645,8 L12.8013,13 L3.19604,13 Z"></path> </g>
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
