@component('Footer Info.support-layout', [
    'pageTitle' => __('support.contact.title'),
    'pageSubtitle' => __('support.contact.subtitle'),
    'active' => 'contact',
])
    <div class="grid gap-5 xl:grid-cols-3">
        @foreach (trans('support.contact.channels') as $channel)
            <article class="group min-w-0 overflow-hidden rounded-[1.5rem] border border-[#ebe7ff] bg-white/95 p-6 shadow-card transition duration-200 hover:-translate-y-1 hover:border-[#cfc7ff] hover:shadow-panel 2xl:p-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#edf0ff] to-[#f7f5ff] font-heading text-[18px] font-extrabold text-[#5d55df] ring-1 ring-[#e2dcff]">
                        {{ mb_substr($channel['label'], 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12px] font-bold uppercase tracking-[0.18em] text-[#6b63f4]">{{ $channel['label'] }}</p>
                        <div class="mt-2 min-w-0 space-y-1 font-heading text-[16px] font-bold leading-7 text-ink sm:text-[17px] 2xl:text-[19px]">
                            @foreach ((array) $channel['value'] as $value)
                                <p class="whitespace-nowrap">{{ $value }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                <p class="mt-5 text-[15px] leading-7 text-copy">{{ $channel['description'] }}</p>
            </article>
        @endforeach
    </div>

    <section class="mt-8 rounded-[1.8rem] border border-[#e7e1ff] bg-white p-7 shadow-card 2xl:p-8">
        <h2 class="font-heading text-[26px] font-bold text-ink">{{ __('support.contact.before_contact_title') }}</h2>
        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach (trans('support.contact.before_contact') as $item)
                <div class="rounded-2xl bg-[#f7f5ff] p-6">
                    <p class="font-heading text-[19px] font-bold text-ink">{{ $item['title'] }}</p>
                    <p class="mt-3 text-[15px] leading-7 text-copy">{{ $item['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endcomponent
