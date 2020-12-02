<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Card;
use Epayco\Epayco;
use Exception;
use Auth;
use Setting;

class CardResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{

            $cards = Card::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->get();
            return $cards; 

        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $userId = Auth::user()->id;

        if($request->has('stripe_token')){
            try{

                $customer_id = $this->customer_id();
                $this->set_stripe();
                $customer = \Stripe\Customer::retrieve($customer_id);
                $card = $customer->sources->create(["source" => $request->stripe_token]);

                $exist = Card::where('user_id', $userId)
                                ->where('last_four',$card['last4'])
                                ->where('brand',$card['brand'])
                                ->where('type', 'stripe')
                                ->count();

                if($exist == 0){

                    $create_card = new Card;
                    $create_card->user_id = $userId;
                    $create_card->card_id = $card['id'];
                    $create_card->last_four = $card['last4'];
                    $create_card->brand = $card['brand'];
                    $create_card->type = 'stripe';
                    $create_card->save();

                }else{
                    if($request->ajax()){
                        return response()->json(['message' => trans('api.card_already')]);
                    }else{
                        return back()->with('flash_error',trans('api.card_already'));
                    }     
                }

                if($request->ajax()){
                    return response()->json(['message' => trans('api.card_added')]); 
                }else{
                    return back()->with('flash_success',trans('api.card_added'));
                }

            } catch(Exception $e){
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                }else{
                    return back()->with('flash_error',$e->getMessage());
                }
            }
        }else if($request->has('epayco_token')){
            try{
                $token = $request->epayco_token;
                $epayco = $this->set_epayco();
                $customer_id = $this->epayco_customer_id($epayco, $token);
                $customer = $epayco->customer->get($customer_id);
                
                $card = $this->find_epayco_card($customer->data->cards, $token);
                if($card == null){
                    $exist = 0;
                }else{
                    $exist = Card::where('user_id', $userId)
                                    ->where('last_four',$card->mask)
                                    ->where('brand',$card->franchise)
                                    ->where('type', 'epayco')
                                    ->count();
                }
                
                if($exist == 0){

                    $create_card = new Card;
                    $create_card->user_id = $userId;
                    $create_card->card_id = $token;
                    $create_card->last_four = $card->mask;
                    $create_card->brand = $card->franchise;
                    $create_card->type = 'epayco';
                    $create_card->save();

                }else{
                    if($request->ajax()){
                        return response()->json(['message' => trans('api.card_already')]);
                    }else{
                        return back()->with('flash_error',trans('api.card_already'));
                    }     
                }

                if($request->ajax()){
                    return response()->json(['message' => trans('api.card_added')]); 
                }else{
                    return back()->with('flash_success',trans('api.card_added'));
                }
            } catch(Exception $e){
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                }else{
                    return back()->with('flash_error',$e->getMessage());
                }
            }
        }else{
            if($request->ajax()){
                return response()->json(['error' => trans('validation.required')], 400);
            }else{
                return back()->with('flash_error',trans('validation.required'));
            }    
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $this->validate($request,[
                'card_id' => 'required|exists:cards,card_id,user_id,'.Auth::user()->id,
            ]);

        if($request->has('type') && $request->type == 'epayco'){
            try{
                $epayco = $this->set_epayco();
                $card = Card::where('card_id',$request->card_id)->first();

                $customer = $epayco->customer->delete(array(
                    "franchise"  => $card->brand,
                    "mask" => $card->last_four,
                    "customer_id"=>Auth::user()->epayco_cust_id
                ));

                Card::where('card_id',$request->card_id)->delete();
    
                if($request->ajax()){
                    return response()->json(['message' => trans('api.card_deleted')]); 
                }else{
                    return back()->with('flash_success',trans('api.card_deleted'));
                }
    
            } catch(Exception $e){
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                }else{
                    return back()->with('flash_error',$e->getMessage());
                }
            }
        }else{
            try{
                $this->set_stripe();
    
                $customer = \Stripe\Customer::retrieve(Auth::user()->stripe_cust_id);
                $customer->sources->retrieve($request->card_id)->delete();
    
                Card::where('card_id',$request->card_id)->delete();
    
                if($request->ajax()){
                    return response()->json(['message' => trans('api.card_deleted')]); 
                }else{
                    return back()->with('flash_success',trans('api.card_deleted'));
                }
    
            } catch(Exception $e){
                if($request->ajax()){
                    return response()->json(['error' => $e->getMessage()], 500);
                }else{
                    return back()->with('flash_error',$e->getMessage());
                }
            }
        }
    }

    /**
     * setting stripe.
     *
     * @return \Illuminate\Http\Response
     */
    public function set_stripe(){
        return \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));
    }

    /**
     * Get a stripe customer id.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_id()
    {
        if(Auth::user()->stripe_cust_id != null){

            return Auth::user()->stripe_cust_id;

        }else{

            try{

                $stripe = $this->set_stripe();

                $customer = \Stripe\Customer::create([
                    'email' => Auth::user()->email,
                ]);

                User::where('id',Auth::user()->id)->update(['stripe_cust_id' => $customer['id']]);
                return $customer['id'];

            } catch(Exception $e){
                return $e;
            }
        }
    }

    /**
     * setting epayco.
     *
     * @return \Illuminate\Http\Response
     */
    public function set_epayco(){
        return new Epayco(array(
            "apiKey" => Setting::get('epay_public_key', ''),
            "privateKey" => Setting::get('epay_private_key', ''),
            "lenguage" => "ES",
            "test" => false
        ));
    }

    public function epayco_customer_id($epayco, $token){
        $customer_id = Auth::user()->epayco_cust_id;
        if( $customer_id == null){
            try{
                $customer = $epayco->customer->create(array(
                    "token_card" => $token,
                    "name" => Auth::user()->first_name,
                    "last_name" => Auth::user()->last_name, //This parameter is optional
                    "email" => Auth::user()->email,
                    "default" => true,

                    //Optional parameters: These parameters are important when validating the credit card transaction
                    // "city" => "Bogota",
                    // "address" => "Cr 4 # 55 36",
                    // "phone" => "3005234321",
                    // "cell_phone"=> "3010000001",
                ));

                $customer_id = $customer->data->customerId;
                User::where('id',Auth::user()->id)->update(['epayco_cust_id' => $customer_id]);
            } catch(Exception $e){
                return $e;
            }
        }

        return $customer_id;
    }

    public function find_epayco_card($cards, $token){
        if(count($cards) == 0) return null;

        $token_card = null;

        foreach ($cards as $card) {
            if(substr($card->token, -6) == substr($token, -6)) $token_card = $card;
        }

        return $token_card;
    }
}
