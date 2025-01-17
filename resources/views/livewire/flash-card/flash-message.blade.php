@if (session()->has('message'))
    @php
        $messageType = session('message_type', 'success');
        $bgColor = [
            'success' => 'bg-green-500',
            'error' => 'bg-red-500',
            'warning' => 'bg-yellow-500',
        ][$messageType];
    @endphp
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="fixed top-4 left-1/2 transform -translate-x-1/2 {{ $bgColor }} text-white px-4 py-2 rounded shadow-lg"
    >
        <p>{{ session('message') }}</p>
    </div>
@endif
