<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

use DB;
use Exception;
use Setting;

use App\ServiceType;
use App\ServiceQuestion;
use App\ServiceAnswer;
use App\Helpers\Helper;

class ServiceAnswerResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $service, $question)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            $ServiceAnswers = ServiceAnswer::where('question_id', $question)->get();
            return view('admin.service.answer.index', compact('ServiceType', 'ServiceQuestion', 'ServiceAnswers'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($service, $question)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            return view('admin.service.answer.create', compact('ServiceType', 'ServiceQuestion'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $service, $question)
    {
        $this->validate($request, [
            //'answer' => 'required|max:255|unique:service_answers,answer,question_id,\''.$question.'\'',
            'answer' => 'required|max:255',
            'fixed' => 'required|numeric',
            'price' => 'sometimes|nullable|numeric',
            'minute' => 'sometimes|nullable|numeric',
            'hour' => 'sometimes|nullable|numeric',
            'distance' => 'sometimes|nullable|numeric',
//            'status' => 'required',
            'description' => 'sometimes|max:255',
        ]);

        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);

            $answer = new ServiceAnswer;

            $answer->question_id = $question;
            $answer->answer = $request->answer;
            $answer->fixed = $request->fixed;
            $answer->description = $request->description;
            $answer->status = 1;
//            $answer->status = $request->status;

            if(!empty($request->price))
                $answer->price = $request->price;
            else
                $answer->price=0;

            if(!empty($request->minute))
                $answer->minute = $request->minute;
            else
                $answer->minute = 0;

            if(!empty($request->hour))
                $answer->hour = $request->hour;
            else
                $answer->hour = 0;

            if(!empty($request->distance))
                $answer->distance = $request->distance;
            else
                $answer->distance = 0;

            $answer->save();

            return redirect()->route('admin.service.question.answer.index', [$service, $question])->with('flash_success', trans('admin.service_type_msgs.service_answer_saved'));
        } catch (Exception $e) {
            dd("Exception", $e);
            return back()->with('flash_error', trans('admin.service_type_msgs.service_answer_not_found'));
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
    public function edit($service, $question, $answer)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            $ServiceAnswer = ServiceAnswer::where('question_id', $question)
                ->findOrFail($answer);

            return view('admin.service.answer.edit', compact('ServiceType', 'ServiceQuestion', 'ServiceAnswer'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $service, $question, $answer)
    {
        $this->validate($request, [
            'answer' => 'required|max:255',
            'fixed' => 'required|numeric',
            'price' => 'sometimes|nullable|numeric',
            'minute' => 'sometimes|nullable|numeric',
            'hour' => 'sometimes|nullable|numeric',
            'distance' => 'sometimes|nullable|numeric',
//            'status' => 'required',
            'description' => 'sometimes|max:255',
        ]);

        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);

            $Answer['answer'] = $request->answer;
            $Answer['fixed'] = $request->fixed;

            if(!empty($request->price))
                $Answer['price'] = $request->price;
            else
                $Answer['price']=0;

            if(!empty($request->minute))
                $Answer['minute'] = $request->minute;
            else
                $Answer['minute'] = 0;

            if(!empty($request->hour))
                $Answer['hour'] = $request->hour;
            else
                $Answer['hour'] = 0;

            if(!empty($request->distance))
                $Answer['distance'] = $request->distance;
            else
                $Answer['distance'] = 0;

            $Answer['calculator'] = $request->calculator;
//            $Answer['status'] = $request->status;
            $Answer['description'] = $request->description;

            ServiceAnswer::where('id', $answer)->update($Answer);

            return redirect()->route('admin.service.question.answer.index', [$service, $question])->with('flash_success', trans('admin.service_type_msgs.service_answer_update'));
        }

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_type_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($service, $question, $answer)
    {
        try {
            $ServiceAnswer = ServiceAnswer::findOrFail($answer);
            $ServiceAnswer->delete();

            return redirect()
                ->route('admin.service.question.answer.index', [$service, $question])
                ->with('flash_success', trans('admin.service_type_msgs.service_answer_delete'));
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.service.question.index', $service)
                ->with('flash_error', trans('admin.service_type_msgs.service_answer_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceType  $service
     * @param  \App\ServiceQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $service, $question, $answer)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            $ServiceAnswer = ServiceAnswer::findOrFail($answer);
            $ServiceAnswer->update(['status' => 1]);
            return redirect()->route('admin.service.question.answer.index', [$service, $question])->with('flash_success', trans('admin.service_type_msgs._service_answer_enable'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_answer_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceType  $service
     * @param  \App\ServiceQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request, $service, $question, $answer)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            $ServiceAnswer = ServiceAnswer::findOrFail($answer);
            $ServiceAnswer->update(['status' => 0]);
            return redirect()->route('admin.service.question.answer.index', [$service, $question])->with('flash_success', trans('admin.service_type_msgs._service_answer_disable'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_answer_not_found'));
        }
    }
}
