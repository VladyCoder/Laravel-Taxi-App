<?php

namespace App\Http\Controllers\Resource;

use App\SpecialRate;
use App\ServiceType;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Setting;

use Carbon\Carbon;

class SpecialrateResource extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store' ,'update', 'destroy']]);
        $this->perpage = Setting::get('per_page', '10');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $specialrates = SpecialRate::with('service_type')->orderBy('created_at' , 'desc')->paginate($this->perpage);
        $pagination=(new Helper)->formatPagination($specialrates);
        return view('admin.specialrate.index', compact('specialrates', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $servicetypes = ServiceType::all();
        return view('admin.specialrate.create',compact('servicetypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'source' => 'required|max:255',
            's_radius' => 'required|numeric',
            'destination' => 'required|max:255',
            'd_radius' => 'required|numeric',
            'price' => 'required|numeric',
            'service_type_id' => 'required|numeric|exists:service_types,id',
        ]);

        try{
            SpecialRate::create($request->all());
            return back()->with('flash_success', trans('admin.specialrate_msgs.specialrate_saved'));
        }
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.specialrate_msgs.specialrate_not_found'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SpecialRate  $specialrate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return SpecialRate::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SpecialRate  $specialrate
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $servicetypes = ServiceType::all();
            $specialrate = SpecialRate::findOrFail($id);
            return view('admin.specialrate.edit',compact('specialrate', 'servicetypes'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SpecialRate  $specialrate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'source' => 'required|max:255',
            's_radius' => 'required|numeric',
            'destination' => 'required|max:255',
            'd_radius' => 'required|numeric',
            'price' => 'required|numeric',
            'service_type_id' => 'required|numeric|exists:service_types,id',
        ]);

        try {
           $specialrate = SpecialRate::findOrFail($id);

            $specialrate->name = $request->name;
            $specialrate->description = $request->description;
            $specialrate->source = $request->source;
            $specialrate->s_latitude = $request->s_latitude;
            $specialrate->s_longitude = $request->s_longitude;
            $specialrate->s_radius = $request->s_radius;
            $specialrate->destination = $request->destination;
            $specialrate->d_latitude = $request->d_latitude;
            $specialrate->d_longitude = $request->d_longitude;
            $specialrate->d_radius = $request->d_radius;
            $specialrate->price = $request->price;
            $specialrate->service_type_id = $request->service_type_id;
            $specialrate->status = $request->status;
            $specialrate->save();

            return redirect()->route('admin.specialrate.index')->with('flash_success', trans('admin.specialrate_msgs.specialrate_update'));    
        }
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.specialrate_msgs.specialrate_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SpecialRate  $specialrate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            SpecialRate::find($id)->delete();
            return back()->with('message', trans('admin.specialrate_msgs.specialrate_delete'));
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.specialrate_msgs.specialrate_not_found'));
        }
    }
}
