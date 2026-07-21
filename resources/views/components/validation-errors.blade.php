@if ($errors->any())
    <div role="alert" class="alert alert-error mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.75-3L13.75 4a2 2 0 00-3.5 0l-7 12a2 2 0 001.75 3z" />
        </svg>
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
