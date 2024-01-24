<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ApiFeedbackSender;

    private $categoryValidationRules = [
        'name' => 'required|string|min:2|max:50',
    ];

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
        $user = Auth::user();

        if($user->cannot('create', Category::class)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        // si estoy aki es que puedo crear la categoría

        $validateCategory = Validator::make($request->all(), $this->categoryValidationRules);
        if($validateCategory->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateCategory->errors()
            );
        }

        // si estoy aki es que no hubo errores de validación
        $category = Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);

        return $this->sendSuccess(
            'La categoria se ha creado',
            $category
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
 
        $category = Category::find($id);
        if(! $category) {
            return $this->sendError('No existe esta categoria', 404);
        }

        if($user->cannot('view', $category)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        return $this->sendSuccess('Categoria encontrada', $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $category = Category::find($id);
        if(! $category) {
            return $this->sendError('No existe esta categoria', 404);
        }

        if($user->cannot('update', $category)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        $validateCategory = Validator::make($request->all(), $this->categoryValidationRules);
        if($validateCategory->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateCategory->errors()
            );
        }

        $category->name = $request->name;
        $category->save();

        return $this->sendSuccess('Categoria actualizada', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        $category = Category::find($id);
        if(! $category) {
            return $this->sendError('No existe esta categoria', 404);
        }

        if($user->cannot('delete', $category)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        // if($category->has_related_data) {
        //     return $this->sendError('Este categoria tiene datos asociados así que no se puede borrar', 403);
        // }

        $category->delete();

        return $this->sendSuccess('Categoria borrada', null);
    }
}
