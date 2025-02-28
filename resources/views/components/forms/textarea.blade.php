<div class="form-control">
    @if ($label)
        <label class="flex items-center gap-1 mb-1 text-sm font-medium">
            <span>
                @if ($label)
                    {{ $label }}
                @else
                    {{ $id }}
                @endif
                @if ($required)
                    <x-highlighted text="*" />
                @endif
                @if ($helper)
                    <div class="group">
                        <div class="cursor-pointer text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="w-4 h-4 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute hidden text-xs group-hover:block border-coolgray-400 bg-coolgray-500">
                            <div class="p-4 card-body">
                                {!! $helper !!}
                            </div>
                        </div>
                    </div>
                @endif
            </span>
        </label>
    @endif
    <textarea placeholder="{{ $placeholder }}" {{ $attributes->merge(['class' => $defaultClass]) }}
        @if ($realtimeValidation) wire:model.debounce.200ms="{{ $id }}"
        @else
        wire:model.defer={{ $value ?? $id }}
        wire:dirty.class="input-warning"@endif
        @disabled($disabled) @readonly($readonly) @required($required) id="{{ $id }}" name="{{ $name }}"
        name={{ $id }}  ></textarea>
    @error($id)
        <label class="label">
            <span class="text-red-500 label-text-alt">{{ $message }}</span>
        </label>
    @enderror
</div>
