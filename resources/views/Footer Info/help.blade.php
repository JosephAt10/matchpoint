@component('Footer Info.support-layout', [
    'pageTitle' => __('support.help.title'),
    'pageSubtitle' => __('support.help.subtitle'),
    'active' => 'help',
])
    <section class="rounded-[1.8rem] border border-[#e7e1ff] bg-white p-7 shadow-card 2xl:p-8">
        <h2 class="font-heading text-[32px] font-bold text-ink">{{ __('support.help.faq_title') }}</h2>
        <div class="mt-6 grid gap-5 xl:grid-cols-2">
            @foreach (trans('support.help.faqs') as $faq)
                <details class="rounded-2xl border border-[#e3e7f3] bg-[#fbfcff] p-6 transition hover:border-[#cfc8ff]">
                    <summary class="cursor-pointer font-heading text-[20px] font-bold leading-7 text-ink">{{ $faq['question'] }}</summary>
                    <p class="mt-4 text-[16px] leading-8 text-copy">{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </section>

    <section class="mt-8 rounded-[1.8rem] border border-[#c7f0d8] bg-[#ecfdf3] p-7 shadow-card 2xl:p-8">
        <h2 class="font-heading text-[28px] font-bold text-[#166534]">{{ __('support.help.need_help_title') }}</h2>
        <p class="mt-3 max-w-[760px] text-[17px] leading-8 text-[#166534]">{{ __('support.help.need_help_body') }}</p>
        <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-2xl bg-[#16a34a] px-7 py-4 text-[16px] font-bold text-white shadow-[0_14px_28px_rgba(22,163,74,.22)] transition hover:bg-[#12843d]">{{ __('support.nav.contact') }}</a>
    </section>
@endcomponent
