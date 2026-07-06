<a href="{{ $item['url'] }}" class="d-flex gap-3 align-items-start text-decoration-none text-reset border rounded-3 p-3 mb-2 bg-white">
    <div class="rounded-3 text-bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
        <i class="fa-solid {{ $item['icon'] }} text-primary"></i>
    </div>
    <div class="flex-grow-1 min-w-0">
        <div class="d-flex justify-content-between gap-2 align-items-start">
            <div class="fw-semibold text-truncate">{{ $item['title'] }}</div>
            <span class="badge text-bg-secondary flex-shrink-0">{{ $item['status']->label() }}</span>
        </div>
        <div class="small text-secondary mt-1">
            {{ $item['type'] }}
            @if($item['creator']) oleh {{ $item['creator'] }} @endif
            @if($item['updated_at']) - {{ $item['updated_at']->diffForHumans() }} @endif
        </div>
        @if($item['note'])
            <div class="small text-warning-emphasis mt-2">
                <i class="fa-solid fa-comment-dots me-1"></i>{{ $item['note'] }}
            </div>
        @endif
    </div>
</a>
