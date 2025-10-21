<!--template:Главная - Заказ и доставка-->
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
<div class="main-order-stages p-t_50 p-b_50">
    <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
    <div class="row">
        @foreach($widget->items as $item)
            <div class="col-md-6 col-lg-6">
                <div class="item-step t-a_center">
                    <div>
                        @if($item->slug=='svg01-item-advantages')
                            <svg fill="#ffffff" width="100px" height="100px" viewBox="-41.78 -41.78 206.44 206.44" version="1.1" id="Layer_1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink" style="enable-background:new 0 0 122.88 102.18" xml:space="preserve" stroke="#ffffff" transform="rotate(0)" stroke-width="0.0012288">
					<g id="SVGRepo_bgCarrier" stroke-width="0">
                        <rect x="-41.78" y="-41.78" width="206.44" height="206.44" rx="103.22" fill="#D7B56D" strokewidth="0"></rect>
                    </g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier"> <g> <path d="M0,0h119.01c0.47,0,0.92,0.09,1.33,0.27c0.58,0.25,1.67,1.34,1.9,1.91c0.17,0.41,0.27,0.86,0.27,1.33v23.72 c0,0.47-0.09,0.92-0.27,1.34c-0.18,0.43-0.44,0.81-0.76,1.14l-0.04,0.03c-0.32,0.31-0.69,0.56-1.1,0.73 c-0.41,0.17-0.86,0.27-1.33,0.27H70.58c-0.15,0-0.26-0.12-0.26-0.26V4.13H32.58v6.89L44.87,25c0.35,0.4,0.51,0.9,0.48,1.4 c-0.03,0.49-0.25,0.97-0.65,1.33l-0.02,0.01c-0.18,0.15-0.37,0.27-0.58,0.34c-0.21,0.08-0.44,0.12-0.66,0.12H6.52 c-0.53,0-1.02-0.22-1.36-0.57c-0.35-0.35-0.57-0.83-0.57-1.36c0-0.28,0.06-0.54,0.17-0.78c0.11-0.25,0.27-0.47,0.47-0.65 l12.65-13.83V4.13H0V0L0,0z M31.55,58.58c1.14,0,2.07,0.93,2.07,2.07s-0.93,2.07-2.07,2.07c-1.14,0-2.07-0.93-2.07-2.07 S30.41,58.58,31.55,58.58L31.55,58.58z M18.35,58.58c1.14,0,2.07,0.93,2.07,2.07s-0.93,2.07-2.07,2.07s-2.07-0.93-2.07-2.07 S17.21,58.58,18.35,58.58L18.35,58.58z M32.62,47.47v2.19h0v2.9h86.77c1.92,0,3.5,1.58,3.5,3.5v42.62c0,1.92-1.57,3.5-3.49,3.5 c-38.6,0-77.18,0-115.79,0c-1.91,0-3.5-1.59-3.5-3.5V56.06c0-1.93,1.57-3.5,3.5-3.5h13.68c0-1.7,0-3.39,0-5.09h4.55v2.19h-2.08v2.9 h10.4v-2.9h-2.08v-2.19H32.62L32.62,47.47z M119.02,56.42H89.4v41.9h29.63V56.42L119.02,56.42L119.02,56.42z M85.54,56.42H46.77 v9.33h38.77V56.42L85.54,56.42L85.54,56.42z M42.91,56.42H3.96v41.9h38.95V56.42L42.91,56.42L42.91,56.42z M46.77,98.31h38.77 v-28.7H46.77V98.31L46.77,98.31L46.77,98.31z M94,58.58c0.28,0,0.55,0.06,0.79,0.16l0.01,0.01c0.25,0.1,0.47,0.26,0.66,0.44 c0.19,0.19,0.34,0.42,0.45,0.67c0.1,0.24,0.16,0.51,0.16,0.79s-0.06,0.54-0.16,0.79c-0.11,0.25-0.26,0.48-0.45,0.67l-0.02,0.01 c-0.19,0.18-0.41,0.33-0.66,0.43c-0.24,0.1-0.51,0.16-0.79,0.16c-0.28,0-0.55-0.06-0.79-0.16c-0.24-0.1-0.46-0.25-0.65-0.43 l-0.03-0.02c-0.19-0.19-0.34-0.42-0.44-0.67l-0.01-0.01c-0.1-0.24-0.15-0.5-0.15-0.78c0-0.28,0.06-0.55,0.16-0.79 c0.1-0.25,0.26-0.48,0.45-0.67s0.42-0.34,0.67-0.45l0.01-0.01C93.47,58.63,93.73,58.58,94,58.58L94,58.58L94,58.58z M74.31,4.13 h-0.13v22.73h44.49V4.13H74.31L74.31,4.13L74.31,4.13z M78.11,6.66c0.19-0.18,0.41-0.33,0.66-0.43c0.24-0.1,0.51-0.16,0.79-0.16 c0.28,0,0.55,0.06,0.79,0.16l0.01,0.01c0.25,0.1,0.47,0.26,0.66,0.44c0.19,0.19,0.34,0.42,0.45,0.67c0.1,0.24,0.16,0.51,0.16,0.79 s-0.06,0.55-0.16,0.79l-0.01,0.01c-0.1,0.24-0.25,0.46-0.43,0.64l-0.03,0.03c-0.19,0.18-0.41,0.33-0.65,0.43 c-0.24,0.1-0.51,0.16-0.79,0.16c-0.28,0-0.55-0.06-0.79-0.16c-0.25-0.1-0.48-0.26-0.67-0.45l0,0c-0.19-0.19-0.34-0.42-0.45-0.67 c-0.1-0.24-0.16-0.51-0.16-0.79s0.06-0.54,0.16-0.79c0.11-0.25,0.26-0.48,0.45-0.67L78.11,6.66L78.11,6.66L78.11,6.66z"></path> </g> </g>
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
                    <div class="item-step-info">
                        <div class="heading">{{ $item->caption }}</div>
                        <div class="text">{!! $item->text !!}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
