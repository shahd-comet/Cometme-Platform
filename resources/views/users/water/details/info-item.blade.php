<li class="d-flex mb-4">
    <div class="avatar avatar-sm flex-shrink-0 me-3">
        <span class="avatar-initial rounded-circle bg-label-{{ $color ?? 'success' }}">
            <i class="bx {{ $icon }}"></i>
        </span>
    </div>

    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
        <div class="me-2">
            <p class="mb-0 lh-1">{{ $label }}</p>
            <small class="text-muted">{{ $value ?? '-' }}</small>
        </div>
    </div>
</li>