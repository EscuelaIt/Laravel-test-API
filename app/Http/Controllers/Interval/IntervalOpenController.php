<?php

namespace App\Http\Controllers\Interval;

use App\Models\Interval;
use App\Lib\DateTimeManager;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Interval\ControlIntervalTrait;

class IntervalOpenController extends Controller
{

    use ApiFeedbackSender, ControlIntervalTrait;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($user->cannot('create', Interval::class)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        $validateInterval = Validator::make($request->all(), $this->intervalValidationRules);
        if($validateInterval->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateInterval->errors()
            );
        }

        if(! $this->isProjectIdValid($user, $request->project_id)) {
            return $this->sendError('No estás autorizado para trabajar con ese proyecto', 403);
        }

        if($user->hasOpenInterval) {
            return $this->sendError('No puedes abrir otro intervalo, finaliza antes el que tienes abierto', 403);
        }

        $dateTimeManager = new DateTimeManager();

        $interval = Interval::create([
            'user_id' => $user->id,
            'project_id' => $request->project_id,
            'start_time' => $dateTimeManager->getNow(),
        ]);

        $interval->load('categories');

        return $this->sendSuccess(
            'El intervalo de trabajo se ha creado',
            $interval
        );
    }
}
