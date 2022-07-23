<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class CancellationQuestionController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.booking_cancellation_questions.index'), 401);
        return view('admin.cancellation_questions.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\BookingCancellationQuestion::select(\DB::raw("booking_cancellation_questions.*, booking_cancellation_questions.{$this->ql}question AS question"));

        return \DataTables::of($list)
            ->addColumn('type_text', function ($query) {
                return transLang('cancellation_question_types')[$query->type];
            })
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.booking_cancellation_questions.create'), 401);
        return view('admin.cancellation_questions.create');
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'question_for' => 'required',
            'question' => 'required|max:250|unique:booking_cancellation_questions',
            'en_question' => 'required|max:250|unique:booking_cancellation_questions',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['question_for', 'question', 'en_question', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $bookingCancellationQuestion = new \App\Models\BookingCancellationQuestion();
            $bookingCancellationQuestion->type = $dataArr->question_for;
            $bookingCancellationQuestion->question = $dataArr->question;
            $bookingCancellationQuestion->en_question = $dataArr->en_question;
            $bookingCancellationQuestion->status = $dataArr->status;
            $bookingCancellationQuestion->save();

            // Commit Transaction
            \DB::commit();
            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getUpdate(Request $request)
    {
        abort_unless(hasPermission('admin.booking_cancellation_questions.update'), 401);

        $result = \App\Models\BookingCancellationQuestion::findOrFail($request->id);
        return view('admin.cancellation_questions.update', compact('result'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'question_for' => 'required',
            'question' => "required|max:250|unique:booking_cancellation_questions,question,{$request->id},id",
            'en_question' => "required|max:250|unique:booking_cancellation_questions,en_question,{$request->id},id",
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['question_for', 'question', 'en_question', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $bookingCancellationQuestion = \App\Models\BookingCancellationQuestion::find($request->id);
            if (!blank($bookingCancellationQuestion)) {
                $bookingCancellationQuestion->type = $dataArr->question_for;
                $bookingCancellationQuestion->question = $dataArr->question;
                $bookingCancellationQuestion->en_question = $dataArr->en_question;
                $bookingCancellationQuestion->status = $dataArr->status;
                $bookingCancellationQuestion->save();
            }

            // Commit Transaction
            \DB::commit();
            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.booking_cancellation_questions.delete'), 401);

        \App\Models\BookingCancellationQuestion::where('id', $request->id)->delete();
        return successMessage();
    }
}
