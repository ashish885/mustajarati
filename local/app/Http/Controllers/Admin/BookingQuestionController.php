<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class BookingQuestionController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.booking_questions.index'), 401);

        return view('admin.booking_questions.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\BookingQuestion::select(\DB::raw("booking_questions.*, booking_questions.{$this->ql}question AS question"));

        return \DataTables::of($list)
            ->addColumn('type_text', function ($query) {
                return transLang('booking_question_types')[$query->type];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.booking_questions.create'), 401);
        return view('admin.booking_questions.create');
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'question_for' => 'required|in:1,2',
            'question' => 'required|max:250',
            'en_question' => 'required|max:250',

        ]);
        $dataArr = arrayFromPost(['question_for', 'question', 'en_question']);

        try {
            // Start Transaction
            \DB::beginTransaction();
            $booking_questions = new \App\Models\BookingQuestion();
            $booking_questions->type = $dataArr->question_for;
            $booking_questions->question = $dataArr->question;
            $booking_questions->en_question = $dataArr->en_question;
            $booking_questions->save();

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
        abort_unless(hasPermission('admin.booking_questions.update'), 401);

        $booking_questions = \App\Models\BookingQuestion::findOrFail($request->id);
        return view('admin.booking_questions.update', compact('booking_questions'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'question_for' => 'required|in:1,2',
            'question' => 'required|max:250',
            'en_question' => 'required|max:250',

        ]);
        $dataArr = arrayFromPost(['question_for', 'question', 'en_question']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $booking_questions = \App\Models\BookingQuestion::find($request->id);
            if (!blank($booking_questions)) {
                $booking_questions->type = $dataArr->question_for;
                $booking_questions->question = $dataArr->question;
                $booking_questions->en_question = $dataArr->en_question;
                $booking_questions->save();
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
        abort_unless(hasPermission('admin.booking_questions.delete'), 401);

        \App\Models\BookingQuestion::where('id', $request->id)->delete();
        return successMessage();
    }
}
