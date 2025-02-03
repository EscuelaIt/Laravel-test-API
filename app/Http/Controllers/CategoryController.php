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
     * @OA\Get(
     *  path="/api/categories",
     *  tags={"category"},
     *  summary="Obtener la lista de las categorias de un usuario",
     *  description="Devuelve un array de objetos Category que haya dado de alta un usuario",
     *  operationId="getUserCategories",
     *  @OA\Parameter(ref="#/components/parameters/acceptJsonHeader"),
     *  @OA\Parameter(ref="#/components/parameters/requestedWith"),
     *  @OA\Response(
     *      response=200,
     *      description="Lista de categorias enviada con éxito",
     *      @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", description="Mensaje de respuesta"),
     *         @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     ),
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="No autorizado",
     *      ref="#/components/responses/UnauthenticatedResponse"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Error de servidor",
     *  ),
     *  security={
     *       {"BearerAuth": {}}
     *  }
     * )
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
     * @OA\Post(
     *  path="/api/categories",
     *  tags={"category"},
     *  summary="Añadir una categoria",
     *  description="Crear una categoria en la base de datos asociada a un usuario",
     *  operationId="createCategory",
     *  @OA\Parameter(ref="#/components/parameters/acceptJsonHeader"),
     *  @OA\Parameter(ref="#/components/parameters/requestedWith"),
     *  @OA\RequestBody(
     *      required=true,
     *      description="Objeto de categoria a crear",
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(ref="#/components/schemas/Category")
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="La categoria se ha creado con éxito",
     *      @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", description="Mensaje de respuesta"),
     *         @OA\Property(
     *              property="data",
     *              ref="#/components/schemas/Category"
     *         )
     *     ),
     *  ),
     *  @OA\Response(
     *      response=400,
     *      description="Error de validación",
     *      ref="#/components/responses/ValidationErrorResponse"
     *  ),
     * @OA\Response(
     *      response=403,
     *      description="No autorizado",
     *      ref="#/components/responses/NotAuthorizedResponse"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="No autenticado",
     *      ref="#/components/responses/UnauthenticatedResponse"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Error de servidor",
     *  ),
     *  security={
     *       {"BearerAuth": {}}
     *  }
     * )
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
        // $category = Category::create([
        //     'user_id' => $user->id,
        //     'name' => $request->name,
        // ]);

        $category = $user->categories()->create([
            'name' => $request->name,
        ]);

        return $this->sendSuccess(
            'La categoria se ha creado',
            $category
        );
    }

    /**
     * @OA\Get(
     *  path="/api/categories/{id}",
     *  tags={"category"},
     *  summary="Obtener una categoria concreto",
     *  description="Obtener un objeto de una categoria que corresponda con un identificador dado",
     *  operationId="getCategory",
     *  @OA\Parameter(ref="#/components/parameters/CategoryIdParameter"),
     *  @OA\Parameter(ref="#/components/parameters/acceptJsonHeader"),
     *  @OA\Parameter(ref="#/components/parameters/requestedWith"),
     *  @OA\Response(
     *      response=200,
     *      description="Operación exitosa",
     *      @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", description="Mensaje de respuesta"),
     *         @OA\Property(
     *           property="data",
     *           ref="#/components/schemas/Category"
     *         )
     *      ),
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="No existe esa categoria",
     *      ref="#/components/responses/NotFoundResponse"
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="No autorizado",
     *      ref="#/components/responses/NotAuthorizedResponse"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="No autenticado",
     *      ref="#/components/responses/UnauthenticatedResponse"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Error de servidor",
     *  ),
     *  security={
     *       {"BearerAuth": {}}
     *  }
     * )
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
     * @OA\Put(
     *  path="/api/categories/{id}",
     *  tags={"category"},
     *  summary="Actualiza una categoria",
     *  description="Actualizar una categoria que corresponda con un identificador dado",
     *  operationId="updateCategory",
     *  @OA\Parameter(ref="#/components/parameters/CategoryIdParameter"),
     *  @OA\Parameter(ref="#/components/parameters/acceptJsonHeader"),
     *  @OA\Parameter(ref="#/components/parameters/requestedWith"),
     *  @OA\RequestBody(
     *      required=true,
     *      description="Datos del categoria a actualizar",
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(ref="#/components/schemas/Category")
     *      )
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Operación exitosa",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", description="Mensaje de respuesta"),
     *         @OA\Property(
     *              property="data",
     *              ref="#/components/schemas/Category"
     *         )
     *     ),
     *  ),
     * @OA\Response(
     *      response=400,
     *      description="Error de validación",
     *      ref="#/components/responses/ValidationErrorResponse"
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="No existe esa categoria",
     *      ref="#/components/responses/NotFoundResponse"
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="No autorizado",
     *      ref="#/components/responses/NotAuthorizedResponse"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="No autenticado",
     *      ref="#/components/responses/UnauthenticatedResponse"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Error de servidor",
     *  ),
     *  security={
     *       {"BearerAuth": {}}
     *  }
     * )
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
     * @OA\Delete(
     *  path="/api/categories/{id}",
     *  tags={"category"},
     *  summary="Borrar una categoria",
     *  description="Borrar una categoria indicado en el identificador de la URL",
     *  operationId="deleteCategory",
     *  @OA\Parameter(ref="#/components/parameters/CategoryIdParameter"),
     *  @OA\Parameter(ref="#/components/parameters/acceptJsonHeader"),
     *  @OA\Parameter(ref="#/components/parameters/requestedWith"),
     *  @OA\Response(
     *      response=200,
     *      description="La categoria se ha borrado"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="No autorizado",
     *      ref="#/components/responses/UnauthenticatedResponse"
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="No existe esa categoria",
     *      ref="#/components/responses/NotFoundResponse"
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="No autorizado",
     *      ref="#/components/responses/NotAuthorizedResponse"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Error de servidor",
     *  ),
     *  security={
     *       {"BearerAuth": {}}
     *  }
     * )
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

        return $this->sendSuccess('Categoría borrada', null);
    }
}
