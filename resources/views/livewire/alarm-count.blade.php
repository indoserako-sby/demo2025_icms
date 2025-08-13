<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    @if ($warningCount > 0 || $dangerCount > 0)
        <span
            class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1_5 d-inline-flex align-items-center justify-content-center">
            {{ $dangerCount + $warningCount }}
        </span>
    @endif
</div>
