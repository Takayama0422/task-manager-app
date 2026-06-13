{{-- x-error-message --}}
@if ($errors->any())
    <div class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-700 rounded-xl text-sm font-bold">
        <ul class="space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
