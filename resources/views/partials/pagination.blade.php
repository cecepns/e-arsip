{{--
    Reusable Pagination Component
    Params:
    - currentPage: halaman aktif (int)
    - totalPages: total halaman (int)
    - baseUrl: base url untuk link (string, opsional)
    - showInfo: string info (opsional)
    Usage:
    @include('partials.pagination', [
        'currentPage' => 1,
        'totalPages' => 3,
        'baseUrl' => '#',
        'showInfo' => 'Menampilkan 1-5 dari 100 entries'
    ])
--}}
<div class="table-footer">
    @if(!empty($showInfo))
    <div class="showing-info">
        <span>{{ $showInfo }}</span>
    </div>
    @endif
    <div class="pagination-wrapper">
        <nav>
            <ul class="pagination">
                <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $currentPage == 1 ? '#' : ($baseUrl . '?page=' . ($currentPage - 1)) }}">Previous</a>
                </li>
                @for($i = 1; $i <= $totalPages; $i++)
                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                        @if($currentPage == $i)
                            <span class="page-link">{{ $i }}</span>
                        @else
                            <a class="page-link" href="{{ $baseUrl . '?page=' . $i }}">{{ $i }}</a>
                        @endif
                    </li>
                @endfor
                <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $currentPage == $totalPages ? '#' : ($baseUrl . '?page=' . ($currentPage + 1)) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
