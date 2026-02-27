<?php

namespace App\Http\Controllers;

use App\Models\Retail;
use App\Models\Distributor;
use Illuminate\Http\Request;

class RetailController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            $retails = $distributor ? Retail::where('distributor_id', $distributor->id)->with('distributor')->latest()->get() : collect([]);
        } elseif ($user->hasRole('admin')) {
            $retails = Retail::with('distributor')->latest()->get();
        } else {
            $projectIds = $user->projects->pluck('id');
            $retails = Retail::whereHas('distributor', fn($q) => $q->whereIn('project_id', $projectIds))->with('distributor')->latest()->get();
        }
        
        return view('retails.index', compact('retails'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            $distributors = $distributor ? collect([$distributor]) : collect([]);
        } elseif ($user->hasRole('admin')) {
            $distributors = Distributor::where('status', 'active')->get();
        } else {
            $projectIds = $user->projects->pluck('id');
            $distributors = Distributor::whereIn('project_id', $projectIds)->where('status', 'active')->get();
        }
        
        return view('retails.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'pin' => 'required|string|size:6',
            'status' => 'required|in:active,inactive',
        ]);

        Retail::create($validated);

        return redirect()->route('retails.index')->with('success', 'Retail created successfully');
    }

    public function edit(Retail $retail)
    {
        $user = auth()->user();
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            if (!$distributor || $retail->distributor_id != $distributor->id) {
                abort(403);
            }
            $distributors = collect([$distributor]);
        } elseif ($user->hasRole('admin')) {
            $distributors = Distributor::where('status', 'active')->get();
        } else {
            $projectIds = $user->projects->pluck('id');
            $distributors = Distributor::whereIn('project_id', $projectIds)->where('status', 'active')->get();
        }
        
        return view('retails.edit', compact('retail', 'distributors'));
    }

    public function update(Request $request, Retail $retail)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'pin' => 'required|string|size:6',
            'status' => 'required|in:active,inactive',
        ]);

        $retail->update($validated);

        return redirect()->route('retails.index')->with('success', 'Retail updated successfully');
    }

    public function destroy(Retail $retail)
    {
        $retail->delete();
        return redirect()->route('retails.index')->with('success', 'Retail deleted successfully');
    }
}
