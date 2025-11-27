<?php

namespace App\Http\Controllers;

use App\Models\AppConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function index()
    {
        $config = AppConfig::first();

        return Inertia::render('Config/Index', [
            'config' => $config,
        ]);
    }

    public function update(Request $request)
    {
        $config = AppConfig::first();

        if (!$config) {
            $config = new AppConfig();
        }

        $config->fill($request->only([
            'appStatus',
            'appStatusMessage',
            'enablePremiumSwitch',
            'facebook',
            'latestVersion',
            'forceUpdate',
            'minVersion',
            'notice',
            'noticeAction',
            'privacy',
            'reportReasons',
            'support',
            'teacherNotice',
            'terms',
            'updateInfo',
            'warnings',
            'x',
            'youtube',
        ]));

        $config->save();

        return back()->with('success', 'Configuration updated successfully');
    }

    public function updateTurn(Request $request)
    {
        $config = AppConfig::first();

        if (!$config) {
            return back()->with('error', 'Configuration not found');
        }

        $config->update($request->only([
            'turn',
            'stun',
            'turnName',
            'turnPass',
        ]));

        return back()->with('success', 'TURN/STUN configuration updated');
    }
}
