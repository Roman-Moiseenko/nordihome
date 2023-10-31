<script>
    let counter_video = 0;
    const _name_point_video = 'point-insert-video';
    function _set_point_video() {
        let _html =  '<div id="' + _name_point_video + '"></div>';
        document.write(_html);
    }
    function AddBlock_Video(_params) {
        let el_Insert = document.getElementById(_name_point_video);
        let _block_HTML = '<div id="video-' + counter_video + '" class="relative pl-5 pr-5 xl:pr-10 py-10 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3">' +
            '<a id="delete-' + counter_video + '" for="video-' + counter_video + '" href="" class="text-slate-300 absolute top-0 right-0 mr-4 mt-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x" class="lucide lucide-x stroke-1.5 h-4 w-4"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></a>' +
            '<div class="input-form">' +
            '<input type="text" name="video.url[]" class="form-control " placeholder="Ссылка на видео" value="' + _params.url + '">' +
            '</div>' +
            '<div class="input-form mt-3 ">' +
            '<input type="text" name="video.caption[]" class="form-control " placeholder="Заголовок" value="' + _params.caption + '">' +
            '</div>' +
            '<textarea name="video.text[]" class="form-control sm:mr-2 mt-3 " rows="4" placeholder="Краткое описание">' + _params.description + '</textarea>' +
            '</div>';

        el_Insert.insertAdjacentHTML('beforebegin', _block_HTML);
        let new_el = document.getElementById('delete-' + counter_video);
        counter_video++;
        new_el.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById(new_el.getAttribute('for')).remove();
        });
    }
</script>
<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-8">
        <script>_set_point_video()</script>
        @foreach($product->videos as $video)
            <script>AddBlock_Video({
                    url: {{ $video->url }},
                    caption: {{ $video->caption }},
                    description: {{ $video->description }}
                })</script>
        @endforeach
        @if(count($product->videos) == 0)
            <script>AddBlock_Video({url: '', caption: '', description: ''})</script>
        @endif
    </div>
    <div class="hidden lg:col-span-4 lg:block">
        <div>
            Размещайте видеоматериалы о товаре на сторонних хостингах <br>
            Например, Rutube или YouTube<br>
            Снимайте видео в хорошем качестве, но компактного размера для быстрой загрузке на мобильных телефонах.<br>
            Видео должно быть формата Short - до 3 минут, рекомендация 1 минута.
        </div>
        <div>
            <x-base.button class="w-full mt-4" variant="primary" type="button"
                           onclick="AddBlock_Video({url: '', caption: '', description: ''})"
            >
                <x-base.lucide class="mr-2" icon="file-video-2"/>
                Добавить URL
            </x-base.button>
        </div>
    </div>
</div>

