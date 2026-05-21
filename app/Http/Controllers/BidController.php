<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function index()
    {
        $bids = Bid::with('project')->get();
        return view('bids.index', compact('bids'));
    }

    public function create()
    {
        $projects = Project::where('status', 'open')->get();
        return view('bids.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric'
        ]);

        Bid::create([
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'proposal' => $request->proposal,
        ]);

        return redirect()->route('bids.index')->with('success', 'Bid submitted.');
    }

    public function show(Bid $bid)
    {
        return view('bids.show', compact('bid'));
    }

    public function edit(Bid $bid)
    {
        $projects = Project::all();
        return view('bids.edit', compact('bid', 'projects'));
    }

    public function update(Request $request, Bid $bid)
    {
        $bid->update($request->only(['amount', 'proposal']));
        return redirect()->route('bids.index')->with('success', 'Bid updated.');
    }

    public function destroy(Bid $bid)
    {
        $bid->delete();
        return redirect()->route('bids.index')->with('success', 'Bid deleted.');
    }
}
