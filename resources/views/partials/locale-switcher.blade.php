@php
    $currentLocale = app()->getLocale();
    $locales = [
        'en' => ['label' => __('English'), 'short' => 'EN', 'flagClass' => 'locale-flag-us'],
        'id' => ['label' => __('Bahasa Indonesia'), 'short' => 'ID', 'flagClass' => 'locale-flag-id'],
    ];
    $activeLocale = $locales[$currentLocale] ?? $locales['en'];
    $otherLocales = collect($locales)->except($currentLocale);
@endphp
@once
    <style>
        .locale-flag {
            position: relative;
            display: inline-block;
            height: 14px;
            width: 21px;
            flex-shrink: 0;
            overflow: hidden;
            border-radius: 2px;
            box-shadow: none;
        }

        .locale-flag-us {
            background:
                linear-gradient(90deg, #24346b 0 42%, transparent 42%),
                repeating-linear-gradient(180deg, #c8232c 0 2px, #fff 2px 4px);
        }

        .locale-flag-us::before {
            content: '';
            position: absolute;
            left: 2px;
            top: 2px;
            height: 2px;
            width: 2px;
            border-radius: 999px;
            background: #fff;
            box-shadow: 5px 0 #fff, 10px 0 #fff, 0 4px #fff, 5px 4px #fff, 10px 4px #fff;
        }

        .locale-flag-id {
            background: linear-gradient(180deg, #e31d2d 0 50%, #fff 50% 100%);
        }
    </style>
@endonce
<details class="group relative inline-flex text-left">
    <summary class="flex h-10 min-w-[68px] cursor-pointer list-none items-center justify-center gap-2 rounded-full border border-[#dcd8fb] bg-transparent px-3 text-[13px] font-extrabold tracking-[0.08em] text-[#343a5f] transition hover:border-[#7b74df] hover:text-[#4b57c5] [&::-webkit-details-marker]:hidden" aria-label="{{ __('Change language') }}">
        <span class="locale-flag {{ $activeLocale['flagClass'] }}" aria-hidden="true"></span>
        <span>{{ $activeLocale['short'] }}</span>
    </summary>

    <div class="absolute left-0 top-full z-50 mt-1 min-w-[68px] overflow-hidden rounded-2xl border border-[#dcd8fb] bg-white/95 p-1 shadow-[0_12px_28px_rgba(37,43,80,0.14)] backdrop-blur">
        @foreach ($otherLocales as $locale => $option)
            <a href="{{ route('locale.switch', $locale) }}" class="flex h-9 items-center justify-center gap-2 rounded-xl px-2 text-[13px] font-extrabold tracking-[0.08em] text-[#343a5f] transition hover:bg-[#f3f1ff] hover:text-[#4b57c5]" aria-label="{{ $option['label'] }}">
                <span class="locale-flag {{ $option['flagClass'] }}" aria-hidden="true"></span>
                <span>{{ $option['short'] }}</span>
            </a>
        @endforeach
    </div>
</details>
