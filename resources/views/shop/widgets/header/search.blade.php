<div class="presearch" data-route="{{ route('shop.product.search') }}">
    <div class="presearch-wrapper">
        <input id="pre-search" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');"
               onblur="this.setAttribute('readonly','');">
        <div class="presearch-suggest" style="display: none">
        </div>
        <div class="presearch-control fs-5 opacity-50">
                            <span id="presearch--icon-clear" class="presearch-icon clear" style="display:none;"><i
                                    class="fa-sharp fa-light fa-xmark"></i></span>
            <span id="presearch--icon-search" class="presearch-icon search"><i
                    class="fa-light fa-magnifying-glass"></i></span>
        </div>
    </div>
    <div class="presearch-overlay" style="display: none"></div>
</div>
