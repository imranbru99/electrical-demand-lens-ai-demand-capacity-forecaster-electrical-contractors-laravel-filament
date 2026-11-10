@extends('layouts.marketing')

@section('title', 'Feature Modules — Toggle On/Off for Your Electrical Demand Business')

@section('content')
<section class="wrap section">
    <div class="eyebrow">Modular architecture</div>
    <h1>Run only the modules you need</h1>
    <p class="lede">Every electrical business is different. Enable core demand/capacity modules, then switch niche integrations on or off from the admin panel.</p>

    @foreach($modules as $category => $items)
        <h2 style="margin-top:2rem;text-transform:capitalize;">{{ str_replace('_', ' ', $category) }}</h2>
        <table class="table" style="margin-top:0.75rem;">
            <thead>
            <tr>
                <th>Module</th>
                <th>Status</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $module)
                <tr>
                    <td><strong>{{ $module->name }}</strong><br><small style="color:var(--muted);">{{ $module->key }}</small></td>
                    <td><span class="pill {{ $module->enabled ? 'on' : 'off' }}">{{ $module->enabled ? 'ON' : 'OFF' }}</span></td>
                    <td>{{ $module->description }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
</section>
@endsection
