<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SportResource;
use App\Models\Sport;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::where('is_active', true)
            ->withCount(['leagues', 'matches'])
            ->orderBy('order')
            ->get();

        return SportResource::collection($sports);
    }

    public function show(Sport $sport)
    {
        $sport->load(['leagues' => function ($query) {
            $query->where('is_active', true)
                ->withCount('matches')
                ->with('country')
                ->orderBy('order');
        }]);

        return new SportResource($sport);
    }
}
