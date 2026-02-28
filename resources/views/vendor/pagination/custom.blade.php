@php
    $queryString = request()->query();
    unset($queryString['page']);
@endphp
@if ($paginator->hasPages() || $paginator->total() > 0)
<style>.pagination-custom a:hover{background:#f3f4f6!important;}</style>
<nav class="pagination-custom" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:16px;">
    <div style="display:flex;align-items:center;gap:16px;">
        <span style="font-size:13px;color:#6b7280;">
            Showing <strong>{{ $paginator->firstItem() ?? 0 }}</strong> to <strong>{{ $paginator->lastItem() ?? 0 }}</strong> of <strong>{{ $paginator->total() }}</strong> results
        </span>
        <form method="get" style="display:flex;align-items:center;gap:6px;">
            @foreach($queryString as $k => $v) @if($k !== 'per_page') <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? implode(',', $v) : $v }}"> @endif @endforeach
            <label style="font-size:13px;color:#6b7280;">Rows:</label>
            <select name="per_page" onchange="this.form.submit()" style="padding:4px 8px;font-size:13px;border:1px solid #d1d5db;border-radius:6px;">
                @foreach([10, 15, 25, 50] as $n)
                <option value="{{ $n }}" {{ $paginator->perPage() == $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div style="display:flex;align-items:center;gap:4px;">
        @if ($paginator->onFirstPage())
            <span style="display:inline-block;padding:6px 12px;font-size:13px;color:#9ca3af;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;cursor:not-allowed;">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-block;padding:6px 12px;font-size:13px;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:6px;text-decoration:none;">Prev</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-block;padding:6px 10px;font-size:13px;color:#6b7280;">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-block;padding:6px 12px;font-size:13px;font-weight:600;color:#fff;background:#2563eb;border:1px solid #2563eb;border-radius:6px;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="display:inline-block;padding:6px 12px;font-size:13px;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:6px;text-decoration:none;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-block;padding:6px 12px;font-size:13px;color:#374151;background:#fff;border:1px solid #d1d5db;border-radius:6px;text-decoration:none;">Next</a>
        @else
            <span style="display:inline-block;padding:6px 12px;font-size:13px;color:#9ca3af;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;cursor:not-allowed;">Next</span>
        @endif
    </div>
</nav>
@endif
