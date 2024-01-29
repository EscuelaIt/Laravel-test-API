<?php

namespace App\Http\Controllers\Interval;

use App\Models\Interval;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Interval\ControlIntervalTrait;

class UpdateIntervalCategoryController extends Controller
{
    use ApiFeedbackSender, ControlIntervalTrait;

    public function attachCategory(Request $request, $id) {
        $user = Auth::user();

        $interval = Interval::find($id);
        if(! $interval) {
            return $this->sendError('No existe este intervalo de trabajo', 404);
        }

        if($user->cannot('update', $interval)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        $validateInput = Validator::make($request->all(), $this->intervalCategoryValidationRules);
        if($validateInput->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateInput->errors()
            );
        }

        if(! $this->isCategoryIdValid($user, $request->category_id)) {
            return $this->sendError('No estás autorizado para trabajar con esa categoría', 403);
        }

        $interval->categories()->detach($request->category_id);
        $message = "Categoría desasociada";
        if($request->attached) {
            $interval->categories()->attach($request->category_id);
            $message = "Categoría asociada";
        }

        return $this->sendSuccess($message, null);
    }
}
