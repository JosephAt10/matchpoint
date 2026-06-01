@component('Footer Info.support-layout', [
    'pageTitle' => __('support.terms.title'),
    'pageSubtitle' => __('support.terms.subtitle'),
    'active' => 'terms',
])
    <section class="rounded-[1.8rem] border border-[#e7e1ff] bg-white p-7 shadow-card 2xl:p-8">
        <div class="grid gap-6 xl:grid-cols-2">
            @foreach (trans('support.terms.sections') as $section)
                <article class="rounded-2xl bg-[#fbfcff] p-6">
                    <h2 class="font-heading text-[25px] font-bold leading-tight text-ink">{{ $section['title'] }}</h2>
                    <p class="mt-4 text-[16px] leading-8 text-copy">{{ $section['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endcomponent
