<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bots = TelegramBot::all();
        return view('admin.telegram_bots.index', compact('bots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.telegram_bots.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:telegram_bots,name',
            'token' => 'required|unique:telegram_bots,token',
            'username' => 'nullable',
            'is_active' => 'boolean',
        ]);

        TelegramBot::create($request->all());

        return redirect()->route('admin.telegram_bots.index')
                         ->with('success', 'Bot created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TelegramBot $telegramBot)
    {
        return view('admin.telegram_bots.show', compact('telegramBot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TelegramBot $telegramBot)
    {
        return view('admin.telegram_bots.edit', compact('telegramBot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TelegramBot $telegramBot)
    {
        $request->validate([
            'name' => 'required|unique:telegram_bots,name,'.$telegramBot->id,
            'token' => 'required|unique:telegram_bots,token,'.$telegramBot->id,
            'username' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $telegramBot->update($request->all());

        return redirect()->route('admin.telegram_bots.index')
                         ->with('success', 'Bot updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TelegramBot $telegramBot)
    {
        $telegramBot->delete();

        return redirect()->route('admin.telegram_bots.index')
                         ->with('success', 'Bot deleted successfully.');
    }
}
