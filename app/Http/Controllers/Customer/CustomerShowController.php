<?php

namespace App\Http\Controllers\Customer;

use App\Lib\ApiFeedbackSender;
use App\Models\Customer;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerShowController extends Controller
{
    use ApiFeedbackSender;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $customerId)
    {
        $customer = Customer::with(['projects'])->find($customerId);
        if(!$customer) {
            return $this->sendError(['message' => 'No encontramos este cliente'], 404);
        }
        $user = Auth::user();
        if($user->cannot('view', $customer)) {
            return $this->sendError(['message' => 'No puedes ver este cliente']);
        }

        return $this->sendSuccess('Encontrado un cliente', $customer);
    }
}
