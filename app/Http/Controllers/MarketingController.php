<?php

namespace App\Http\Controllers;

use App\Models\FeatureModule;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'modules' => FeatureModule::orderBy('sort_order')->get(),
            'enabledCount' => FeatureModule::where('enabled', true)->count(),
        ]);
    }

    public function features(): View
    {
        return view('pages.features', [
            'modules' => FeatureModule::orderBy('sort_order')->get(),
        ]);
    }

    public function modules(): View
    {
        return view('pages.modules', [
            'modules' => FeatureModule::orderBy('sort_order')->get()->groupBy('category'),
        ]);
    }

    public function pricing(): View
    {
        return view('pages.pricing');
    }

    public function howItWorks(): View
    {
        return view('pages.how-it-works');
    }

    public function demo(): View
    {
        return view('pages.demo');
    }
}
