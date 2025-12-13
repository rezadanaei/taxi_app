<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZarinpalService;
use App\Models\Payment;
use App\Facades\SMS;

class PaymentController extends Controller
{
    public function verify(Request $request, ZarinpalService $zarinpal)
    {
        $authority = $request->Authority;
        $status = $request->Status;

        $payment = Payment::where('authority', $authority)->firstOrFail();

        if ($status !== 'OK') {
            $payment->update(['status' => 'failed']);
            return "پرداخت لغو شد یا ناموفق بود.";
        }

        $result = $zarinpal->verifyPayment($payment->authority, $payment->amount);

        if ($result['success']) {
            $payment->update([
                'status' => 'success',
                'ref_id' => $result['ref_id'],
            ]);

            if ($payment->payable) {
                $payment->payable->update(['status' => 'paid']);
            }
            if ($payment->payable_type === 'App\Models\Trip' && $payment->payable) {
               
                $driver = $payment->payable->driver;
                if ($driver) {
                    $driverPhone = $driver->phone;
                    $driverFirstName = $driver->userable->first_name ?? '';
                    $driverLastName = $driver->userable->last_name ?? '';

                    SMS::sendPattern($driverPhone, [
                        $driverFirstName,
                        $driverLastName,
                        $payment->payable_id,
                    ],401676);
                } 
            }
            return "پرداخت موفق بود. کد پیگیری: " . $result['ref_id'];
        }

        $payment->update(['status' => 'failed']);
        return "خطا در تایید پرداخت: " . $result['message'];
    }


}
