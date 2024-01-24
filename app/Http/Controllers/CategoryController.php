<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use ApiFeedbackSender;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return $this->sendSuccess(
            "Tenemos {$user->categories->count()} categorias", 
            $user->categories,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
