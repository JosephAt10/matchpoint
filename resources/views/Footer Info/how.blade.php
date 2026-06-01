@component('Footer Info.support-layout', [
    'pageTitle' => __('support.how.title'),
    'pageSubtitle' => __('support.how.subtitle'),
    'active' => 'how',
])
    <div class="grid gap-6 xl:grid-cols-2">
        @foreach (trans('support.how.steps') as $step)
            <article class="rounded-[1.6rem] border border-[#e7e1ff] bg-white p-7 shadow-card 2xl:p-8">
                <p class="text-[14px] font-bold uppercase tracking-[0.18em] text-[#5a38d6]">{{ $step['eyebrow'] }}</p>
                <h2 class="mt-4 font-heading text-[28px] font-bold leading-tight text-ink">{{ $step['title'] }}</h2>
                <p class="mt-4 text-[17px] leading-8 text-copy">{{ $step['body'] }}</p>
            </article>
        @endforeach
    </div>
@endcomponent
