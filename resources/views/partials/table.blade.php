{{--
    Reusable Table Component
    Usage Example:
    @include('partials.table', [
        'tableId' => 'activityTable',
        'tableClass' => 'activity-table',
        'thead' => '...',
        'tbody' => '...'
    ])
--}}
<div class="table-responsive">
    <table @if(isset($tableId)) id="{{ $tableId }}" @endif class="{{ $tableClass ?? '' }}">
        {{-- Table Header --}}
        @if(isset($thead))
            {!! $thead !!}
        @endif
        {{-- Table Body --}}
        @if(isset($tbody))
            {!! $tbody !!}
        @endif
    </table>
</div>
