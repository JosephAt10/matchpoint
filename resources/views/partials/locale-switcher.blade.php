@php
    $currentLocale = app()->getLocale();
@endphp
<div class="inline-flex items-center gap-2 rounded-full border border-[#e5e0fb] bg-white/90 px-2 py-1 text-[13px] font-medium text-[#4f5579] shadow-sm">
    <a href="{{ route('locale.switch', 'en') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 transition {{ $currentLocale === 'en' ? 'bg-[#f1edff] text-[#5a38d6]' : 'hover:bg-[#f8f6ff]' }}" aria-label="{{ __('English') }}">
        <span>🇬🇧</span>
        <span>EN</span>
    </a>
    <a href="{{ route('locale.switch', 'id') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 transition {{ $currentLocale === 'id' ? 'bg-[#f1edff] text-[#5a38d6]' : 'hover:bg-[#f8f6ff]' }}" aria-label="{{ __('Bahasa Indonesia') }}">
        <span>🇮🇩</span>
        <span>ID</span>
    </a>
</div>
