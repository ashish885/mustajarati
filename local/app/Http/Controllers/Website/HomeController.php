<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\WebsiteController;
use Illuminate\Http\Request;

class HomeController extends WebsiteController
{
    public function getIndex(Request $request)
    {
        $testimonials = \App\Models\Testimonial::getData();
        return view("website.{$this->locale}.home.index", compact('testimonials'));
    }

    public function getChangeLocale(Request $request)
    {
        \Session::put('lang', $request->lang);
        return redirect()->back();
    }

    public function getAboutUs(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.user.about_us", compact('showContentOnly', 'locale'));
    }

    public function getCancellationPolicy(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.user.cancellation_policy", compact('showContentOnly', 'locale'));
    }

    public function getTermsConditions(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.user.terms_conditions", compact('showContentOnly', 'locale'));
    }

    public function getPrivacyPolicy(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.user.privacy_policy", compact('showContentOnly', 'locale'));
    }

    public function getVendorTermsConditions(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.vendor.terms_conditions", compact('showContentOnly', 'locale'));
    }

    public function getVendorPrivacyPolicy(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.vendor.privacy_policy", compact('showContentOnly', 'locale'));
    }

    public function getVendorAboutUs(Request $request)
    {
        $locale = $request->lang ? $request->lang : $this->locale;
        $showContentOnly = $request->lang ? true : false;
        return view("website.{$locale}.cms.vendor.about_us", compact('showContentOnly', 'locale'));
    }

    public function postSendDownloadLink(Request $request)
    {
        $this->validate($request, [
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile_no' => 'required|numeric|digits_between:6,20',
        ]);
        $dataArr = arrayFromPost(['dial_code', 'mobile_no']);

        try {
            // http://localhost/mustajarati/download/mobile/app
            \App\Jobs\SMS\SendAppShareLink::dispatch(['dial_code' => $dataArr->dial_code, 'mobile' => $dataArr->mobile_no, 'locale' => $this->locale]);

            return successMessage('message_send');
        } catch (\Exception $e) {
            return errorMessage($e->getMessage(), true);
        }
    }

    public function getDownloadMobileApp(Request $request)
    {
        return view("website.download-app");
    }
    
    public function getCategories(Request $request)
    {
        return view("website.{$this->locale}.home.categories");
    }
    
    public function getContactUs(Request $request)
    {
        return view("website.{$this->locale}.cms.contact_us");
    }

    public function postContactUs(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email',
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile_no' => 'required|numeric|digits_between:9,20',
            'message' => 'required|string|max:500',
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile_no', 'message']);

        try {
            $inquiry = new \App\Models\Inquiry();
            $inquiry->name = $dataArr->name;
            $inquiry->email = strtolower($dataArr->email);
            $inquiry->dial_code = $dataArr->dial_code;
            $inquiry->mobile = $dataArr->mobile_no;
            $inquiry->message = $dataArr->message;
            $inquiry->save();
            
            return successMessage();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
