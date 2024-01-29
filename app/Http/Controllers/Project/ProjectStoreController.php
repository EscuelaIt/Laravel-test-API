<?php

namespace App\Http\Controllers\Project;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectStoreController extends Controller
{
    use ApiFeedbackSender, ControlProjectTrait;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($user->cannot('create', Project::class)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        $validateProject = Validator::make($request->all(), $this->projectValidationRules);
        if($validateProject->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateProject->errors()
            );
        }

        if(! $this->isCustomerIdValid($user, $request->customer_id)) {
            return $this->sendError('No puedes crear proyectos de cliente que no te pertenece', 403);
        }

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'customer_id' => $request->customer_id,
        ]);

        return $this->sendSuccess(
            'El projecto se ha creado',
            $project
        );
    }
}
