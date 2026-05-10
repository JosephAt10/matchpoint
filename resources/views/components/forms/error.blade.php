@props(['field'])

@error($field)
    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
@enderror