@props([
    'label' => null,
    'name' => '',
    'type' => 'text',
    'value' => '',
    'error' => null,
    'required' => false,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-zinc-800">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => 'w-full min-h-[44px] px-3.5 py-2.5 bg-white border rounded-xl text-[16px] sm:text-sm text-zinc-900 transition focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent ' . ($error || $errors->has($name) ? 'border-red-400 focus:ring-red-500' : 'border-zinc-300')
        ]) }}
    />

    @if ($error || $errors->has($name))
        <p class="text-xs font-medium text-red-600 mt-1">
            {{ $error ?: $errors->first($name) }}
        </p>
    @endif
</div>
