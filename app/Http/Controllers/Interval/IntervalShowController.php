<?php

namespace App\Http\Controllers\Interval;

use App\Models\Interval;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IntervalShowController extends Controller
{
    use ApiFeedbackSender;

    public function show(string $id)
    {
        $user = Auth::user();

        $interval = Interval::where('id', $id)->first();
        if(! $interval) {
            return $this->sendError('No existe este intervalo de trabajo', 404);
        }

        info($interval);
        info($user);
        if($user->cannot('view', $interval)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        return $this->sendSuccess('Intervalo encontrado', $interval);
    }
}
