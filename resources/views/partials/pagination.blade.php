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
                    <a class="page-link" href="{{ $currentPage == 1 ? '#' : ($baseUrl . '?' . http_build_query(array_merge(request()->query(), ['page' => $currentPage - 1]))) }}">Previous</a>
                </li>
                @php
                    // Smart pagination logic
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    
                    // Always show first page
                    if ($start > 1) {
                        $start = 1;
                        $end = min(5, $totalPages);
                    }
                    
                    // Always show last page if close
                    if ($end < $totalPages && $totalPages <= 10) {
                        $end = $totalPages;
                    }
                @endphp
                
                {{-- Show first page with ellipsis if needed --}}
                @if($start > 1)
                    <li class="page-item {{ $currentPage == 1 ? 'active' : '' }}">
                        @if($currentPage == 1)
                            <span class="page-link">1</span>
                        @else
                            <a class="page-link" href="{{ $baseUrl . '?' . http_build_query(array_merge(request()->query(), ['page' => 1])) }}">1</a>
                        @endif
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif
                
                {{-- Show page numbers --}}
                @for($i = $start; $i <= $end; $i++)
                    @if($i > 1 || $start == 1)
                        <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                            @if($currentPage == $i)
                                <span class="page-link">{{ $i }}</span>
                            @else
                                <a class="page-link" href="{{ $baseUrl . '?' . http_build_query(array_merge(request()->query(), ['page' => $i])) }}">{{ $i }}</a>
                            @endif
                        </li>
                    @endif
                @endfor
                
                {{-- Show last page with ellipsis if needed --}}
                @if($end < $totalPages)
                    @if($end < $totalPages - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item {{ $currentPage == $totalPages ? 'active' : '' }}">
                        @if($currentPage == $totalPages)
                            <span class="page-link">{{ $totalPages }}</span>
                        @else
                            <a class="page-link" href="{{ $baseUrl . '?' . http_build_query(array_merge(request()->query(), ['page' => $totalPages])) }}">{{ $totalPages }}</a>
                        @endif
                    </li>
                @endif
                <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $currentPage == $totalPages ? '#' : ($baseUrl . '?' . http_build_query(array_merge(request()->query(), ['page' => $currentPage + 1]))) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
