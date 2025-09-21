{{--
    Reusable Modal Component
    Params:
    - id: string (modal id)
    - title: string (judul modal)
    - body: string (isi modal, HTML)
    - footer: string (isi footer, HTML, opsional)
    - size: string (opsional, contoh: 'modal-lg', 'modal-sm')
    - centered: bool (opsional, default true)
    Usage:
    @include('partials.modal', [
        'id' => 'detailModal',
        'title' => 'Detail Surat',
        'body' => '<p>Isi detail di sini</p>',
        'footer' => '<button class="btn btn-primary">OK</button>',
        'size' => 'modal-lg',
        'centered' => true
    ])
--}}
<div class="modal fade" id="{{ $id }}" aria-hidden="true" aria-labelledby="{{ $id }}Label" tabindex="-1">
    <div class="modal-dialog {{ $size ?? 'modal-lg' }}{{ (isset($centered) && $centered !== false) ? ' modal-dialog-centered' : '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $body !!}
            </div>
            @if(!empty($footer))
            <div class="modal-footer">
                {!! $footer !!}
            </div>
            @endif
        </div>
    </div>
</div>
