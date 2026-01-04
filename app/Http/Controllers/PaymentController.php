<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZarinpalService;
use App\Models\Payment;
use App\Facades\SMS;
use App\Services\PushNotificationService;

class PaymentController extends Controller
{
    // public function verify(Request $request, ZarinpalService $zarinpal)
    // {
    //     $authority = $request->Authority;
    //     $status = $request->Status;

    //     $payment = Payment::where('authority', $authority)->firstOrFail();

    //     if ($status !== 'OK') {
    //         $payment->update(['status' => 'failed']);
    //         return "پرداخت لغو شد یا ناموفق بود.";
    //     }

    //     $result = $zarinpal->verifyPayment($payment->authority, $payment->amount);

    //     if ($result['success']) {
    //         $payment->update([
    //             'status' => 'success',
    //             'ref_id' => $result['ref_id'],
    //         ]);

    //         if ($payment->payable) {
    //             $payment->payable->update(['status' => 'paid']);
    //         }
    //         if ($payment->payable_type === 'App\Models\Trip' && $payment->payable) {
               
    //             $driver = $payment->payable->driver;
    //             if ($driver) {
    //                 $driverPhone = $driver->phone;
    //                 $driverFirstName = $driver->userable->first_name ?? '';
    //                 $driverLastName = $driver->userable->last_name ?? '';

    //                 SMS::sendPattern($driverPhone, [
    //                     $driverFirstName,
    //                     $driverLastName,
    //                     $payment->payable_id,
    //                 ],401676);
                    
    //                 $driverId = (int)$driver->id;
                   
    //                 $pushService = new PushNotificationService();
    //                 $pushService->sendToUsers(
    //                     userId: $driverId,
    //                     title: 'پرداخت هزینه سفر',
    //                     body: "مسافر هزینه سفر {$payment->payable_id} را پرداخت کرد",
    //                     data: [
    //                         'type'       => 'peyment_trip',
    //                         'trip_id'    => $payment->payable_id,
                            
    //                     ]
    //                 );
    //             } 
    //         }
    //         return "پرداخت موفق بود. کد پیگیری: " . $result['ref_id'];
    //     }

    //     $payment->update(['status' => 'failed']);
    //     return "خطا در تایید پرداخت: " . $result['message'];
    // }

    public function verify(Request $request, ZarinpalService $zarinpal)
    {
        $authority = $request->Authority;
        $status = $request->Status;

        $payment = Payment::where('authority', $authority)->firstOrFail();
        $payableType = $payment->payable_type;
        $payableId = $payment->payable_id;

        $paymentTitle = $this->getPaymentTitle($payableType, $payableId);

        if ($status !== 'OK') {
            $payment->update(['status' => 'failed']);
            
            return view('payment_result', [
                'success' => false,
                'title' => 'پرداخت ناموفق',
                'message' => 'پرداخت لغو شد یا ناموفق بود.',
                'paymentTitle' => $paymentTitle,
                'amount' => number_format($payment->amount) . ' ریال',
                'payableId' => $payableId,
                'redirectTo' => route('cart'), 
                'redirectDelay' => 30, 
            ]);
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

            if ($payableType === 'App\Models\Trip' && $payment->payable) {
                $this->sendDriverNotifications($payment);
            }

            return view('payment_result', [
                'success' => true,
                'title' => 'پرداخت موفق',
                'message' => 'پرداخت شما با موفقیت انجام شد.',
                'paymentTitle' => $paymentTitle,
                'amount' => number_format($payment->amount) . ' ریال',
                'payableId' => $payableId,
                'trackingCode' => $result['ref_id'],
                'redirectTo' => route('user.profile'),
                'redirectDelay' => 30, 
            ]);
        }

        $payment->update(['status' => 'failed']);
        
        return view('payment_result', [
            'success' => false,
            'title' => 'خطا در پرداخت',
            'message' => 'خطا در تایید پرداخت: ' . ($result['message'] ?? 'خطای نامشخص'),
            'paymentTitle' => $paymentTitle,
            'amount' => number_format($payment->amount) . ' ریال',
            'payableId' => $payableId,
            'redirectTo' => route('payment.retry', $payment->id), 
            'redirectDelay' => 30, 
        ]);
    }

    
    private function getPaymentTitle($payableType, $payableId)
    {
        $types = [
            'App\Models\Trip' => 'هزینه سفر',
            'App\Models\Order' => 'سفارش',
            'App\Models\Wallet' => 'شارژ کیف پول',
            'App\Models\Subscription' => 'اشتراک',
        ];

        $typeName = $types[$payableType] ?? 'تراکنش';
        
        return $typeName . ' شماره ' . $payableId;
    }

    
    private function sendDriverNotifications($payment)
    {
        $driver = $payment->payable->driver;
        if ($driver) {
            $driverPhone = $driver->phone;
            $driverFirstName = $driver->userable->first_name ?? '';
            $driverLastName = $driver->userable->last_name ?? '';

            SMS::sendPattern($driverPhone, [
                $driverFirstName,
                $driverLastName,
                $payment->payable_id,
            ], 401676);
            
            $pushService = new PushNotificationService();
            $pushService->sendToUsers(
                userId: (int)$driver->id,
                title: 'پرداخت هزینه سفر',
                body: "مسافر هزینه سفر {$payment->payable_id} را پرداخت کرد",
                data: [
                    'type' => 'payment_trip',
                    'trip_id' => $payment->payable_id,
                ]
            );
        }
    }

    public function retry(Request $request, ZarinpalService $zarinpal)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->withErrors('احراز هویت انجام نشد.');
        }

        $request->validate([
            'payment_id' => 'required|integer|exists:payments,id',
        ]);

        $payment = Payment::where('id', $request->payment_id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['failed', 'pending'])
            ->first();

        if (!$payment) {
            return redirect()->back()->withErrors('پرداخت قابل تلاش مجدد نیست.');
        }

        $trip = Trip::find($payment->payable_id);
        if (!$trip) {
            return redirect()->back()->withErrors('سفر مرتبط با پرداخت یافت نشد.');
        }

        $commissionPercent = $this->normalizeTariff(tariff('commission'));
        $commissionToman   = $trip->cost * ($commissionPercent / 100);
        $amount            = round($commissionToman * 10);

        $payment->update([
            'amount'    => $amount,
            'status'    => 'pending',
            'authority' => null,
        ]);

        $result = $zarinpal->requestPayment([
            'amount'       => $amount,
            'description'  => 'تلاش مجدد پرداخت کمیسیون سفر شماره ' . $trip->id,
            'callback_url' => route('payment.verify'),
            'mobile'       => $user->phone ?? null,
            'email'        => $user->email ?? null,
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);

            return redirect()
                ->back()
                ->withErrors($result['message'] ?? 'خطا در اتصال به درگاه پرداخت');
        }

        $payment->update([
            'authority' => $result['authority'],
        ]);

        return redirect($result['payment_url']);
    }



}
