@extends('layouts.marketing')

@section('title', 'Pricing — ElectricalDemandLens Pilot Plans for Electrical Contractors')

@section('content')
<section class="wrap section">
    <div class="eyebrow">Pilot pricing hypothesis</div>
    <h1>Simple plans for growing electrical teams</h1>
    <p class="lede">Start with a focused pilot, then expand modules and electrician seats as your demand volume grows.</p>

    <div class="grid-3" style="margin-top:2rem;">
        <div class="price">
            <h3>Starter Pilot</h3>
            <div class="amt">$299<span style="font-size:1rem;color:var(--muted);">/mo</span></div>
            <p>1 location · weekly forecasts · AI drafts · audit log</p>
        </div>
        <div class="price" style="border-color:var(--sky);box-shadow:0 12px 30px rgba(31,142,207,.15);">
            <h3>Ops Pilot</h3>
            <div class="amt">$799<span style="font-size:1rem;color:var(--muted);">/mo</span></div>
            <p>Multi-tech capacity plans · gap alerts · Google Calendar · SMS consent</p>
        </div>
        <div class="price">
            <h3>Scale Pilot</h3>
            <div class="amt">$1,499<span style="font-size:1rem;color:var(--muted);">/mo</span></div>
            <p>Jobber / ServiceTitan / QuickBooks hooks · attendance hub · priority support</p>
        </div>
    </div>
</section>
@endsection
