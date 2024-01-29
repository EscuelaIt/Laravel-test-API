<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Lib\ApiFeedbackSender;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Customer\ControlCustomerTrait;

class CustomerUpdateController extends Controller
{
    use ApiFeedbackSender, ControlCustomerTrait;
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $id)
    {
        $user = Auth::user();

        $customer = Customer::find($id);
        if(! $customer) {
            return $this->sendError('No existe este cliente', 404);
        }

        if($user->cannot('update', $customer)) {
            return $this->sendError('No estás autorizado para realizar esta acción', 403);
        }

        $validateCustomer = Validator::make($request->all(), $this->customerValidationRules);
        if($validateCustomer->fails()){
            return $this->sendValidationError(
                'Ha ocurrido un error de validación',
                $validateCustomer->errors()
            );
        }

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->telephone = $request->telephone;
        $customer->save();

        return $this->sendSuccess('Cliente actualizado', $customer);
    }
}
