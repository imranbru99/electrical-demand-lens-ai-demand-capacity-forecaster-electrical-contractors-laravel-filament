@extends('layouts.marketing')

@section('title', 'ElectricalDemandLens — AI Demand & Capacity Forecaster for Electrical Contractors')
@section('meta_description', 'Forecast weekly electrical workload and resource demand from historical service requests, field technicians, and job tickets — with AI drafts, human approval, and audit logs.')

@section('content')
<section class="wrap hero">
    <div>
        <div class="eyebrow">Electrical Contractors · Home & Field Services</div>
        <h1>ElectricalDemandLens</h1>
        <p class="lede">AI demand &amp; capacity forecaster that projects weekly workload, matches electrician hours to surge risk, and turns service requests plus job tickets into approved staffing plans — with human approval and audit logs.</p>
        <div class="cta-row">
            <a class="btn btn-primary" href="/admin">Launch forecast board</a>
            <a class="btn btn-ghost" href="{{ route('features') }}">Explore all features</a>
        </div>
        <p style="margin-top:1.25rem;color:var(--muted);font-size:0.95rem;">{{ $enabledCount }} modules enabled in this demo · Pilot pricing $299–$1,499/mo</p>
    </div>
    <div class="panel">
        <h3>This week · demand lens</h3>
        <div class="slot"><strong>Mon–Fri · 34 projected jobs</strong><span>118h demand vs 105h capacity · util 112%</span></div>
        <div class="slot"><strong>Wednesday gap · storm outage surge</strong><span>Shortfall ~9h · recommend overtime + contractor</span></div>
        <div class="slot"><strong>EV charger installs · ZIP 75201</strong><span>High risk specialty · 2 licensed electricians available</span></div>
    </div>
</section>

<section class="wrap section">
    <h2>Built for electrical ops leads</h2>
    <p class="lede">Replace spreadsheet guessing with an intake → AI forecast → human approval → capacity publish loop.</p>
    <div class="grid-3" style="margin-top:1.5rem;">
        <div class="feature">
            <h3>Forecast the week</h3>
            <p>Project jobs and hours by service type, ZIP, and day using open requests and seasonal factors.</p>
        </div>
        <div class="feature">
            <h3>Plan capacity early</h3>
            <p>Match electrician specialties and daily capacity against projected demand before the surge hits.</p>
        </div>
        <div class="feature">
            <h3>Approve with proof</h3>
            <p>Publish staffing plans only after ops approval, with an immutable audit trail for compliance.</p>
        </div>
    </div>
</section>

<section class="wrap section">
    <h2>Modules at a glance</h2>
    <div class="grid-2" style="margin-top:1.25rem;">
        @foreach($modules->take(8) as $module)
            <div class="feature">
                <div style="display:flex;justify-content:space-between;gap:0.75rem;align-items:center;margin-bottom:0.4rem;">
                    <h3 style="margin:0;">{{ $module->name }}</h3>
                    <span class="pill {{ $module->enabled ? 'on' : 'off' }}">{{ $module->enabled ? 'ON' : 'OFF' }}</span>
                </div>
                <p>{{ $module->description }}</p>
            </div>
        @endforeach
    </div>
    <div class="cta-row">
        <a class="btn btn-ghost" href="{{ route('modules') }}">View every module</a>
    </div>
</section>
@endsection
