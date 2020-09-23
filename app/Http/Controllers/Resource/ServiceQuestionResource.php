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

class ServiceQuestionResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $service)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestions = ServiceQuestion::where('service_id', $service)->with('answers')->get();
            return view('admin.service.question.index', compact('ServiceType', 'ServiceQuestions'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($service)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            return view('admin.service.question.create', compact('ServiceType'));
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
    public function store(Request $request, $service)
    {
        $this->validate($request, [
            //'question' => 'required|max:255|unique:service_questions',
            'question' => 'required|max:255',
            'description' => 'sometimes|max:255',
//            'status' => 'required',
        ]);

        try {
            $ServiceType = ServiceType::findOrFail($service);

            $ServiceQuestion = ServiceQuestion::create([
                'service_id' => $service,
                'question' => $request->question,
                'description' => $request->description,
                'status' => 1
//                'status' => $request->status
            ]);
            ServiceAnswer::create([
                'question_id' => $ServiceQuestion->id,
                'answer' => trans('admin.service.Yes'),
                'status' => 1
            ]);
            ServiceAnswer::create([
                'question_id' => $ServiceQuestion->id,
                'answer' => trans('admin.service.No'),
                'status' => 1
            ]);
            return redirect()->route('admin.service.question.index', $service)->with('flash_success', trans('admin.service_type_msgs.service_question_saved'));
        }

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_type_not_found'));
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
    public function edit($service, $question)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::where('service_id', $service)
                ->findOrFail($question);

            return view('admin.service.question.edit', compact('ServiceType', 'ServiceQuestion'));
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
    public function update(Request $request, $service, $question)
    {
        $this->validate($request, [
            //'question' => 'required|max:255|unique:service_questions,question,'.$question,
            'question' => 'required|max:255',
            'description' => 'sometimes|max:255',
//            'status' => 'required',
        ]);

        try {
            $ServiceType = ServiceType::findOrFail($service);

            $Question['question'] = $request->question;
            $Question['description'] = $request->description;
//            $Question['status'] = $request->status;

            ServiceQuestion::where('id', $question)->update($Question);

//            $ServiceQuestion = ServiceQuestion::findOrFail($question);
//            $ServiceQuestion->question = $request->question;
//            $ServiceQuestion->description = $request->description;
//            $ServiceQuestion->status = $request->status;
//
//            $ServiceQuestion->save();

            return redirect()->route('admin.service.question.edit', [$service, $question])->with('flash_success', trans('admin.service_type_msgs.service_question_update'));
        }

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_question_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($service, $question)
    {
        try {
            $ServiceQuestion = ServiceQuestion::findOrFail($question);

            $ServiceAnswers = ServiceAnswer::where('question_id', $question)->get();
            if (count($ServiceAnswers)) {
                foreach ($ServiceAnswers as $ServiceAnswer_Indx => $ServiceAnswer) {
                    $ServiceAnswer->delete();
                }
            }

            $ServiceQuestion->delete();
            return redirect()
                ->route('admin.service.question.index', $service)
                ->with('flash_success', trans('admin.service_type_msgs.service_question_delete'));
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.service.question.index', $service)
                ->with('flash_error', trans('admin.service_type_msgs.service_question_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceType  $service
     * @param  \App\ServiceQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $service, $question)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            $ServiceQuestion->update(['status' => 1]);
            return redirect()->route('admin.service.question.index', $service)->with('flash_success', trans('admin.service_type_msgs.service_question_enable'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_question_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ServiceType  $service
     * @param  \App\ServiceQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request, $service, $question)
    {
        try {
            $ServiceType = ServiceType::findOrFail($service);
            $ServiceQuestion = ServiceQuestion::findOrFail($question);
            $ServiceQuestion->update(['status' => 0]);
            return redirect()->route('admin.service.question.index', $service)->with('flash_success', trans('admin.service_type_msgs.service_question_disable'));
        } catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.service_type_msgs.service_question_not_found'));
        }
    }
}
