<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SendPushNotification;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\StripeInvalidRequestError;

use Epayco\Epayco;

use Auth;
use Setting;
use Exception;

use App\Card;
use App\User;
use App\WalletPassbook;
use App\UserRequests;
use App\UserRequestPayment;
use App\WalletRequests;
use App\Provider;
use App\Fleet;

use App\Http\Controllers\ProviderResources\TripController;

class PaymentController extends Controller
{
       /**
     * payment for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required|exists:user_request_payments,request_id|exists:user_requests,id,paid,0,user_id,'.Auth::user()->id
        ]);


        $UserRequest = UserRequests::find($request->request_id);
        
        $tip_amount=0;

        if($UserRequest->payment_mode == 'CARD') {

            $RequestPayment = UserRequestPayment::where('request_id',$request->request_id)->first(); 
            
            if(isset($request->tips) && !empty($request->tips)){
                $tip_amount=round($request->tips,2);
            }
            
            $ChargeAmount = $RequestPayment->payable+$tip_amount;
            $currency = Setting::get('currency', 'COP');
            
            try {
                if($ChargeAmount  == 0){

                    $RequestPayment->payment_mode = 'CARD';
                    $RequestPayment->card = $RequestPayment->payable;
                    $RequestPayment->payable = 0;
                    $RequestPayment->tips = $tip_amount;                
                    $RequestPayment->provider_pay = $RequestPayment->provider_pay+$tip_amount;
                    $RequestPayment->save();

                    $UserRequest->paid = 1;
                    $UserRequest->status = 'COMPLETED';
                    $UserRequest->save();

                    //for create the transaction
                    (new TripController)->callTransaction($request->request_id);

                    if($request->ajax()) {
                    return response()->json(['message' => trans('api.paid')]); 
                    } else {
                        return redirect('dashboard')->with('flash_success', trans('api.paid'));
                    }
                }else{
                    $Card = Card::where('user_id',Auth::user()->id)->where('is_default',1)->first();

                    if($Card->type == 'epayco'){
                        $epayco = new Epayco(array(
                            "apiKey" => Setting::get('epay_public_key', ''),
                            "privateKey" => Setting::get('epay_private_key', ''),
                            "lenguage" => "ES",
                            "test" => false
                        ));

                        $Charge = $epayco->charge->create(array(
                            "token_card" => $Card->card_id,
                            "customer_id" => Auth::user()->epayco_cust_id,
                            "doc_type" => "CC",
                            "doc_number" => "1234566789",
                            "name" => Auth::user()->first_name,
                            "last_name" => Auth::user()->last_name,
                            "email" => Auth::user()->email,
                            "description" => "Payment Charge for ".Auth::user()->email,
                            "value" => $ChargeAmount,
                            "tax" => "0",
                            "tax_base" => $ChargeAmount,
                            "currency" => $currency,
                        ));

                        if($Charge->status && $Charge->success){
                            $payment_id = $Charge->data->ref_payco;
                        }else{
                            if($request->ajax()){
                                return response()->json(['error' => $Charge->message], 500);
                            } else {
                                return back()->with('flash_error', $Charge->message);
                            }
                        }

                    }else{
                        $stripe_secret = Setting::get('stripe_secret_key');
                        Stripe::setApiKey(Setting::get('stripe_secret_key'));

                        $Charge = Charge::create(array(
                            "amount" => $ChargeAmount * 100,
                            "currency" => $currency,
                            "customer" => Auth::user()->stripe_cust_id,
                            "card" => $Card->card_id,
                            "description" => "Payment Charge for ".Auth::user()->email,
                            "receipt_email" => Auth::user()->email
                        ));

                        $payment_id = $Charge["id"];
                    }

                    $RequestPayment->payment_id = $payment_id;
                    $RequestPayment->payment_mode = 'CARD';
                    $RequestPayment->card = $RequestPayment->payable;
                    $RequestPayment->payable = 0;
                    $RequestPayment->tips = $tip_amount;
                    $RequestPayment->provider_pay = $RequestPayment->provider_pay+$tip_amount;
                    $RequestPayment->save();

                    $UserRequest->paid = 1;
                    $UserRequest->status = 'COMPLETED';
                    $UserRequest->save();

                    //for create the transaction
                    (new TripController)->callTransaction($request->request_id);

                    if($request->ajax()) {
                        return response()->json(['message' => trans('api.paid')]); 
                    } else {
                        return redirect('dashboard')->with('flash_success', trans('api.paid'));
                    }
                }
            } catch(StripeInvalidRequestError $e){
            
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                } else {
                    return back()->with('flash_error', $e->getMessage());
                }
            } catch(Exception $e) {
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                } else {
                    return back()->with('flash_error', $e->getMessage());
                }
            }
        }
    }

    /**
     * payment for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_payment(Request $request)
    {
        $this->validate($request, [
            'request_id' => 'required|exists:user_request_payments,request_id|exists:user_requests,id,paid,0,user_id,'.Auth::user()->id
        ]);

        $UserRequest = UserRequests::find($request->request_id);

        $tip_amount=0;

        if($UserRequest->payment_mode == 'CARD') {
            $RequestPayment = UserRequestPayment::where('request_id',$request->request_id)->first();

            if(isset($request->tips) && !empty($request->tips)){
                $tip_amount=round($request->tips,2);
            }

            $StripeCharge = ($RequestPayment->payable+$tip_amount) * 100;
            $currency = Setting::get('currency', 'COP');

            try {
                $Card = Card::where('user_id',Auth::user()->id)->where('is_default',1)->first();
                $stripe_secret = Setting::get('stripe_secret_key');

                Stripe::setApiKey(Setting::get('stripe_secret_key'));

                if($StripeCharge  == 0){
                    $RequestPayment->payment_mode = 'CARD';
                    $RequestPayment->card = $RequestPayment->payable;
                    $RequestPayment->payable = 0;
                    $RequestPayment->tips = $tip_amount;
                    $RequestPayment->provider_pay = $RequestPayment->provider_pay+$tip_amount;
                    $RequestPayment->save();

                    $UserRequest->paid = 1;
                    $UserRequest->status = 'COMPLETED';
                    $UserRequest->save();

                    //for create the transaction
                    (new TripController)->callTransaction($request->request_id);

                    if($request->ajax()) {
                        return response()->json(['message' => trans('api.paid')]);
                    } else {
                        return redirect('dashboard')->with('flash_success', trans('api.paid'));
                    }
                }else{
                    $Charge = Charge::create(array(
                        "amount" => $StripeCharge,
                        "currency" => $currency,
                        "customer" => Auth::user()->stripe_cust_id,
                        "card" => $Card->card_id,
                        "description" => "Payment Charge for ".Auth::user()->email,
                        "receipt_email" => Auth::user()->email
                    ));

                    /*$ProviderCharge = (($RequestPayment->total+$RequestPayment->tips - $RequestPayment->tax) - $RequestPayment->commision) * 100;

                    $transfer = Transfer::create(array(
                        "amount" => $ProviderCharge,
                        "currency" => $currency,
                        "destination" => $Provider->stripe_acc_id,
                        "transfer_group" => "Request_".$UserRequest->id,
                      )); */

                    $RequestPayment->payment_id = $Charge["id"];
                    $RequestPayment->payment_mode = 'CARD';
                    $RequestPayment->card = $RequestPayment->payable;
                    $RequestPayment->payable = 0;
                    $RequestPayment->tips = $tip_amount;
                    $RequestPayment->provider_pay = $RequestPayment->provider_pay+$tip_amount;
                    $RequestPayment->save();

                    $UserRequest->paid = 1;
                    $UserRequest->status = 'COMPLETED';
                    $UserRequest->save();

                    //for create the transaction
                    (new TripController)->callTransaction($request->request_id);

                    if($request->ajax()) {
                        return response()->json(['message' => trans('api.paid')]);
                    } else {
                        return redirect('dashboard')->with('flash_success', trans('api.paid'));
                    }
                }
            } catch(StripeInvalidRequestError $e){
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                } else {
                    return back()->with('flash_error', $e->getMessage());
                }
            } catch(Exception $e) {
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                } else {
                    return back()->with('flash_error', $e->getMessage());
                }
            }
        }
    }

    /**
     * add wallet money for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_money(Request $request){
        $this->validate($request, [
            'amount' => 'required|integer',
            'card_id' => 'required|exists:cards,card_id,user_id,'.Auth::user()->id
        ]);

        try{
            
            $WalletCharge = $request->amount;
            $currency = Setting::get('currency', 'COP');
            
            Card::where('user_id',Auth::user()->id)->update(['is_default' => 0]);
            Card::where('card_id',$request->card_id)->update(['is_default' => 1]);

            $Card = Card::where('card_id',$request->card_id)->where('user_id',Auth::user()->id)->where('is_default',1)->first();

            if($Card->type == 'epayco'){
                $epayco = new Epayco(array(
                    "apiKey" => Setting::get('epay_public_key', ''),
                    "privateKey" => Setting::get('epay_private_key', ''),
                    "lenguage" => "ES",
                    "test" => false
                ));

                $Charge = $epayco->charge->create(array(
                    "token_card" => $request->card_id,
                    "customer_id" => Auth::user()->epayco_cust_id,
                    "doc_type" => "CC",
                    "doc_number" => "1234566789",
                    "name" => Auth::user()->first_name,
                    "last_name" => Auth::user()->last_name,
                    "email" => Auth::user()->email,
                    "description" => "Payment Charge for ".Auth::user()->email,
                    "value" => $WalletCharge,
                    "tax" => "0",
                    "tax_base" => $WalletCharge,
                    "currency" => $currency,
                ));

                if(!$Charge->status){
                    if($request->ajax()){
                        return response()->json(['error' => $Charge->message], 500);
                    } else {
                        return back()->with('flash_error', $Charge->message);
                    }
                }

            }else{
                Stripe::setApiKey(Setting::get('stripe_secret_key'));

                $Charge = Charge::create(array(
                    "amount" => $WalletCharge * 100,
                    "currency" => $currency,
                    "customer" => Auth::user()->stripe_cust_id,
                    "card" => $request->card_id,
                    "description" => "Adding Money for ".Auth::user()->email,
                    "receipt_email" => Auth::user()->email
                    ));
            }

            //sending push on adding wallet money
            (new SendPushNotification)->WalletMoney(Auth::user()->id,currency($request->amount));

            //for create the user wallet transaction
            (new TripController)->userCreditDebit($request->amount,Auth::user()->id,1);

            $wallet_balance=Auth::user()->wallet_balance+$request->amount;

            if($request->ajax()){
                return response()->json(['success' => currency($request->amount)." ".trans('api.added_to_your_wallet'), 'message' => currency($request->amount)." ".trans('api.added_to_your_wallet'), 'balance' => $wallet_balance]); 
            } else {
                return redirect('wallet')->with('flash_success',currency($request->amount).trans('admin.payment_msgs.amount_added'));
            }

        } catch(StripeInvalidRequestError $e) {
            if($request->ajax()){
                return response()->json(['error' => $e->getMessage()], 500);
            }else{
                return back()->with('flash_error',$e->getMessage());
            }
        } catch(Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            } else {
                return back()->with('flash_error', $e->getMessage());
            }
        }
    }

    /**
     * send money to provider or fleet.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_money(Request $request, $id){
        try{
            $Requests = WalletRequests::where('id',$id)->first();

            if($Requests->request_from=='provider') {
                $provider = Provider::find($Requests->from_id);
                $stripe_cust_id=$provider->stripe_cust_id;
                $email=$provider->email;
            } else{
                $fleet = Fleet::find($Requests->from_id);
                $stripe_cust_id=$fleet->stripe_cust_id;
                $email=$fleet->email;
            }

            if(empty($stripe_cust_id)){              
                throw new Exception(trans('admin.payment_msgs.account_not_found'));
            }

            $StripeCharge = $Requests->amount * 100;
            $currency = Setting::get('currency', 'COP');

            Stripe::setApiKey(Setting::get('stripe_secret_key'));

            $tranfer = \Stripe\Transfer::create(array(
                     "amount" => $StripeCharge,
                     "currency" => $currency,
                     "destination" => $stripe_cust_id,
                     "description" => "Payment Settlement for ".$email                     
                 ));           

            //create the settlement transactions
            (new TripController)->settlements($id);

             $response=array();
            $response['success']=trans('admin.payment_msgs.amount_send');
           
        } catch(Exception $e) {
            $response['error']=$e->getMessage();           
        }

        return $response;
    }
}
