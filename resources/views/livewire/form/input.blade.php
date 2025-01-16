@props([
    'type' => 'text',
    'name',
    'label',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'autofocus' => false,
    'wire' => '',
    'xData' => '',
    'xModel' => '',
    'xOn' => [],
])

<div>
    <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
        @if($required)
            <span style="color: red;">*</span>
        @endif
    </label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($autofocus) autofocus @endif
        @if($wire) wire:model{{ $wire }}="{{ $name }}" @endif
        @if($xData) x-data="{{ $xData }}" @endif
        @if($xModel) x-model="{{ $xModel }}" @endif
        @foreach($xOn as $event => $handler)
            x-on:{{ $event }}="{{ $handler }}"
        @endforeach
        {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) }}
    >
    @error($name)
    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
