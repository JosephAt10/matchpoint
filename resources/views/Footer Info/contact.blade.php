@component('Footer Info.support-layout', [
    'pageTitle' => __('support.contact.title'),
    'pageSubtitle' => __('support.contact.subtitle'),
    'active' => 'contact',
])
    <div class="grid gap-6 xl:grid-cols-3">
        @foreach (trans('support.contact.channels') as $channel)
            <article class="rounded-[1.6rem] border border-[#e7e1ff] bg-white p-7 shadow-card 2xl:p-8">
                <p class="text-[14px] font-bold uppercase tracking-[0.18em] text-[#5a38d6]">{{ $channel['label'] }}</p>
                <h2 class="mt-4 font-heading text-[26px] font-bold leading-tight text-ink 2xl:text-[30px]">{{ $channel['value'] }}</h2>
                <p class="mt-4 text-[16px] leading-8 text-copy">{{ $channel['description'] }}</p>
            </article>
        @endforeach
    </div>

    <section class="mt-8 rounded-[1.8rem] border border-[#e7e1ff] bg-white p-7 shadow-card 2xl:p-8">
        <h2 class="font-heading text-[30px] font-bold text-ink">{{ __('support.contact.before_contact_title') }}</h2>
        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach (trans('support.contact.before_contact') as $item)
                <div class="rounded-2xl bg-[#f7f5ff] p-6">
                    <p class="font-heading text-[21px] font-bold text-ink">{{ $item['title'] }}</p>
                    <p class="mt-3 text-[16px] leading-8 text-copy">{{ $item['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endcomponent
