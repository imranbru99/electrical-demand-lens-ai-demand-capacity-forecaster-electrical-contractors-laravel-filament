@extends('layouts.marketing')

@section('title', 'Demo Users — ElectricalDemandLens Admin Login Credentials')

@section('content')
<section class="wrap section">
    <div class="eyebrow">Try the product</div>
    <h1>Demo users</h1>
    <p class="lede">All demo accounts use password <strong>password</strong>. Admin panel: <a href="/admin">/admin</a></p>

    <table class="table" style="margin-top:1.5rem;">
        <thead>
        <tr>
            <th>Role</th>
            <th>Email</th>
            <th>Password</th>
            <th>Access</th>
        </tr>
        </thead>
        <tbody>
        <tr><td>Admin</td><td>admin@electricaldemandlens.test</td><td>password</td><td>Full panel + modules</td></tr>
        <tr><td>Owner</td><td>owner@electricaldemandlens.test</td><td>password</td><td>Ops lead approvals</td></tr>
        <tr><td>Staff / Planner</td><td>staff@electricaldemandlens.test</td><td>password</td><td>Intake, forecasts, capacity</td></tr>
        <tr><td>Technician</td><td>tech@electricaldemandlens.test</td><td>password</td><td>Field / job ticket view</td></tr>
        <tr><td>Customer (demo)</td><td>customer@electricaldemandlens.test</td><td>password</td><td>Portal demo identity</td></tr>
        </tbody>
    </table>
</section>
@endsection
