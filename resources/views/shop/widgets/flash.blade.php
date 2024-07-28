<div class="container-xl mt-3 mb-3">
    @if(Session::has('danger'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-light fa-triangle-exclamation"></i>
            {{ Session::get('danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-light fa-circle-check"></i>
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif
    @if(Session::has('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-light fa-shield-exclamation"></i>
            {{ Session::get('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif
    @if(Session::has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa-light fa-shield-check"></i>
            {{ Session::get('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif
</div>
