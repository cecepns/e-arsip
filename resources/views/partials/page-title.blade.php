{{--
    Reusable Page Title Component
    Params:
    - title: string (judul utama)
    - subtitle: string (opsional, subjudul)
    Usage:
    @include('partials.page-title', [
        'title' => 'Dashboard',
        'subtitle' => 'Kelola surat masuk dan surat keluar dengan mudah.'
    ])
--}}

{{-- ANCHOR: Set browser tab title using @section directive --}}
@section('title', $title . ' | E-Arsip')

<div class="page-title">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2>{{ $title }}</h2>
        </div>
        @if(!empty($subtitle))
        <div>
            <p class="page-subtitle mb-0">{{ $subtitle }}</p>
        </div>
        @endif
    </div>
</div>