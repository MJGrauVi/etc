<?php

namespace App\Http\Controllers;

use App\Models\Red;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRedRequest;

class RedController extends Controller
{
    public function index()
    {
        return response()->json(Red::all());
    }

    public function show(Red $red)
    {
        return response()->json($red);
    }

    public function store(StoreRedRequest $request)
    {
        $red = Red::create($request->validated());

        return response()->json($red, 201);
    }

    public function update(Request $request, Red $red)
    {
        $red->update($request->all());

        return response()->json($red);
    }

    public function destroy(Red $red)
    {
        $red->delete();

        return response()->json(null, 204);
    }
}
