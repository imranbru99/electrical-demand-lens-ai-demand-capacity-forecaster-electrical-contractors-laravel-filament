@extends('layouts.marketing')

@section('title', 'How ElectricalDemandLens Works — Intake to Capacity Audit Loop')

@section('content')
<section class="wrap section">
    <div class="eyebrow">MVP workflow</div>
    <h1>1 intake trigger → draft → approval → audit</h1>
    <p class="lede">The core automation loop designed for electrical contractors who need accurate weekly staffing without losing control.</p>

    <div class="steps" style="margin-top:2rem;">
        <div class="step">
            <h3>Intake trigger</h3>
            <p>Service requests and historical job tickets arrive from phone, web, Jobber, or ServiceTitan with estimated hours and ZIP.</p>
        </div>
        <div class="step">
            <h3>AI draft / action</h3>
            <p>ElectricalDemandLens projects weekly demand, scores surge risk, and drafts capacity plans with gap notes.</p>
        </div>
        <div class="step">
            <h3>Human approval</h3>
            <p>Owner or ops planner reviews the AI draft, adjusts staffing, and approves publishable actions.</p>
        </div>
        <div class="step">
            <h3>Publish + audit log</h3>
            <p>Approved forecasts become capacity plans and job tickets. Every step is written to the compliance audit trail.</p>
        </div>
    </div>
</section>
@endsection
