@php
if($size == 'sm') {
    $line_classes = 'w-6 h-2';
    $dot_classes = 'h-4 w-4';
    $label_text_size = 'text-xs';
} else if($size == 'md') {
    $line_classes = 'w-8 h-3';
    $dot_classes = 'h-5 w-5';
    $label_text_size = 'text-sm';
} else if($size == 'lg') {
    $line_classes = 'w-10 h-4';
    $dot_classes = 'h-6 w-6';
    $label_text_size = 'text-sm';
} else if($size == 'xl') {
    $line_classes = 'w-12 h-5';
    $dot_classes = 'h-7 w-7';
    $label_text_size = 'text-lg';
}
@endphp

<div class="flex items-center w-full toggle-container">
    <label for="{{ $attributes['id'] }}" class="flex items-center cursor-pointer">
        <!-- toggle -->
        <div class="relative">
            <!-- input -->
            <input type="checkbox" class="sr-only form-toggle" {!! $attributes -> merge() !!} />
            <!-- line -->
            <div class="{{ $line_classes }} bg-gray-300 rounded-full shadow-inner"></div>
            <!-- dot -->
            <div class="dot absolute {{ $dot_classes }} bg-white rounded-full shadow transition"></div>
        </div>
        <!-- label -->
        <div class="ml-3 text-gray-700 font-medium {{ $label_text_size }}">
            {{ $label }}
        </div>
    </label>
</div>
