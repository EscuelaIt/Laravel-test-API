<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerStoreController extends Controller
{

    use ApiFeedbackSender, ControlCustomerTrait;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($user->cannot('create', Customer::class)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        $validateCustomer = Validator::make($request->all(), $this->customerValidationRules);
        if($validateCustomer->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateCustomer->errors()
            );
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'user_id' => $user->id,
        ]);

        // $customer = $user->customers()->create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'telephone' => $request->telephone,
        // ]);

        return $this->sendSuccess(
            'El cliente se ha creado',
            $customer
        );
    }
}
