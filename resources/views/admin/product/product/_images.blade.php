<div class="w-full">
    <div>
        Первое изображение становится основным для продукта. Перемещайте изображения стрелками для выбора главного и
        сортировки.
        Для каждого изображения можно установить подпись ALT
    </div>
    <div id="block-images" class="flex-wrap flex">
        <div id="point-load-images"></div>
    </div>
    <x-base.button class="mt-4" variant="primary" type="button" data-tw-toggle="modal"
                   data-tw-target="#static-backdrop-modal-preview">
        <x-base.lucide class="mr-2" icon="image-plus"/>
        Загрузить изображения
    </x-base.button>
</div>
<script>
    const pointLoadImage = document.getElementById('point-load-images');
    const blockImages = document.getElementById('block-images');
    LoadImages(true);
    let _listImages = [];

    function _getImageHTML(_image) {
        let _id = _image.id;
        return '' +
            '<div class="p-5 border-2 border-dashed rounded-md shadow-sm border-slate-200/60 image-drag-drop" data-sort="' + _image.sort + '">' +
                '<div class="relative h-40 w-40 mx-auto cursor-pointer image-fit zoom-in">' +
                    '<img class="rounded-md" src="' + _image.url + '" alt="' + _image.alt + '" title="' + _image.alt + '"/>' +
                    '<div id="delete-photo-' + _id + '" data-id="' + _id + '" title="Удалить фотографию?" class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white bg-danger right-0 top-0 -mr-2 -mt-2"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="x" data-lucide="x" class="lucide lucide-x w-4 h-4"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> </div>' +
                '</div>' +
                '<div class="relative mx-auto mt-5 cursor-pointer flex items-center" style="justify-content: space-between;">' +
                    '<a href="" id="up-photo-' + _id + '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg></a>' +
                    _buttonDropDown(_image.id, _image.alt) +
                    '<a href="" id="down-photo-' + _id + '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg></a>' +
                '</div>' +
            '</div>';
    }

    function _buttonDropDown(_id, _alt) {
        return '<div class="text-center">' +
                    '<div class="dropdown inline-block" data-tw-placement="bottom-start">' +
                        '<button class="dropdown-toggle btn btn-primary" aria-expanded="false" data-tw-toggle="dropdown" type="button">ALT</button>' +
                        '<div class="dropdown-menu">' +
                            '<div class="dropdown-content">' +
                                '<div class="p-2">' +
                                    '<div class="mt-3">' +
                                        '<input id="input-alt-' + _id + '" type="text" class="form-control mt-2 flex-1" placeholder="ALT" value="' + _alt + '" />' +
                                    '</div>' +
                                    '<div class="flex items-center mt-3">' +
                                        '<button data-tw-dismiss="dropdown" class="btn btn-secondary w-32 ml-auto" type="button">Отмена</button>' +
                                        '<button data-tw-dismiss="dropdown"  id="alt-photo-' + _id + '" class="btn btn-primary w-32 ml-2" type="button">Сохранить</button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
    }

    //Функция загрузки всех изображений
    function LoadImages(preload = false) {
        //AJAX
        let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + {{ $product->id }};
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/{{ $product->id }}/get-images');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                _listImages = JSON.parse(request.responseText);
                blockImages.innerHTML = '';
                _listImages.forEach(function (item) {
                    blockImages.insertAdjacentHTML('beforeend', _getImageHTML(item));
                    document.getElementById('delete-photo-' + item.id).addEventListener('click', function (e) {
                        DeleteImage(item.id)
                    });
                    document.getElementById('up-photo-' + item.id).addEventListener('click', function (e) {
                        e.preventDefault();
                        UpImage(item.id)
                    });
                    document.getElementById('down-photo-' + item.id).addEventListener('click', function (e) {
                        e.preventDefault();
                        DownImage(item.id)
                    });
                    document.getElementById('alt-photo-' + item.id).addEventListener('click', function (e) {
                        e.preventDefault();
                        AltImage(item.id)
                    });
                });
                setTimeout(function () {
                    _updateTippy();
                    return true;
                }, 1000)
            }
        };
    }

    //Функция UP и Down
    function UpImage(id_image) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + {{ $product->id }} + '&photo_id=' + id_image;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/{{ $product->id }}/up-image');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) LoadImages();
        }
    }

    function DownImage(id_image) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + {{ $product->id }} + '&photo_id=' + id_image;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/{{ $product->id }}/down-image');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                LoadImages();
            }
        }
    }

    function AltImage(id_image) {
        let _alt = document.getElementById('input-alt-' + id_image).value;
        let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + {{ $product->id }} + '&photo_id=' + id_image + '&alt=' + _alt;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/{{ $product->id }}/alt-image');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                LoadImages();
            }
        }
    }

    function DeleteImage(id_image) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + {{ $product->id }} + '&photo_id=' + id_image;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/{{ $product->id }}/del-image');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) LoadImages();
        }
    }

    function _updateTippy() {
        // Tooltips
        window.$(".tooltip").each(function () {
            let options = {
                content: window.$(this).attr("title"),
            };

            if ($(this).data("trigger") !== undefined) {
                options.trigger = window.$(this).data("trigger");
            }

            if ($(this).data("placement") !== undefined) {
                options.placement = window.$(this).data("placement");
            }

            if ($(this).data("theme") !== undefined) {
                options.theme = window.$(this).data("theme");
            }

            if ($(this).data("tooltip-content") !== undefined) {
                options.content = window.$(window.$(this).data("tooltip-content"))[0];
            }

            window.$(this).removeAttr("title");

            window.tippy(this, {
                arrow: window.roundArrow,
                animation: "shift-away",
                ...options,
            });
        });
    }
</script>

@once
    @push('styles')
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
    @endpush
@endonce

<script>
    (function () {
        "use strict"
        let blockImages = $("#block-images");
        blockImages.sortable({
            stop: function( event, ui ) {
                let sort = Number(ui.item.attr('data-sort'))
                let prev = ui.item.prev()
                let next = ui.item.next()
                let delta;
                if (prev.length !== 0) {
                    delta = sort - prev.data('sort')
                } else {
                    delta = sort - next.data('sort') + 1;
                }

                //Отправляем сохранять, если верно
                if (delta !== 0) {
                    let new_sort = [];
                    blockImages.find('.image-drag-drop').each(function() {
                        new_sort.push(Number($(this).attr('data-sort')));
                    })

                    $.post('/admin/product/{{ $product->id }}/move-image', {
                        _token: '{{ csrf_token() }}',
                        new_sort: new_sort,
                    }, function (data) {
                        if (data === true) {
                            let _i = 0;
                            blockImages.find('.image-drag-drop').each(function() {
                                $(this).attr('data-sort', _i);
                                _i++;
                            })
                        }
                    })
                }
            }
        });
    })();
</script>
