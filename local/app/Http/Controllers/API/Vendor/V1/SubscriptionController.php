<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class SubscriptionController extends VendorController
{
    public function getCurrentPlanDetails(Request $request)
    {
        $vendor = getTokenUser();
        try {
            $subscription_stats = [];
            $subscription_stats['total_sponsor_items'] = $vendor->total_sponsor_items;
            $subscription_stats['used_sponsor_items'] = $vendor->used_sponsor_items;

            $products = \App\Models\Product::select(\DB::raw("products.id, products.default_image, products.{$this->ql}name AS name, products.sponsor_start_date, products.sponsor_end_date, products.is_sponsored"))
                ->where('products.vendor_id', $vendor->id)
                ->orderBy('products.is_sponsored', 'DESC')
                ->get();

            $services = \App\Models\Service::select(\DB::raw("services.id, services.default_image, services.{$this->ql}name AS name, services.sponsor_start_date, services.sponsor_end_date, services.is_sponsored"))
                ->where('services.vendor_id', $vendor->id)
                ->orderBy('services.is_sponsored', 'DESC')
                ->get();

            return apiResponse('success', compact('subscription_stats', 'products', 'services'));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getList(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|min:1',
        ]);

        try {
            $list = \App\Models\SubscriptionPlan::select(\DB::raw("id, {$this->ql}name AS name, amount, item_sponsor_duration, total_items"))
                ->where('status', 1)
                ->orderBy('amount')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postAddSponsorItem(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'type' => 'required|in:product,service',
            'id' => ('required|numeric|' . ($request->type == 'product' ? 'exists:products' : 'exists:services')),
        ]);
        $dataArr = arrayFromPost(['type', 'id']);

        try {
            $item = $dataArr->type == 'product' ? \App\Models\Product::find($request->id) : \App\Models\Service::find($request->id);
            if ($item->is_sponsored) {
                return errorMessage("{$dataArr->type}_already_sponsored");
            }

            $subscription_plan = \App\Models\VendorSubscriptionHistory::where('vendor_id', $vendor->id)
                ->where('remaining_items', '>', 0)
                ->where('status', 2)
                ->orderBy('id', 'DESC')
                ->first();
            if (blank($subscription_plan)) {
                return errorMessage('plan_item_sponsored');
            }

            // Start Transaction
            \DB::beginTransaction();

            $item->is_sponsored = 1;
            $item->sponsor_start_date = date('Y-m-d');
            $item->sponsor_end_date = date('Y-m-d', strtotime("+{$subscription_plan->item_sponsor_duration} DAY"));
            $item->save();

            $subscription_plan->remaining_items--;
            $subscription_plan->save();

            $vendor->used_sponsor_items++;
            $vendor->save();

            // Create Log
            $sponsorLog = new \App\Models\VendorSponsorItemLog();
            $sponsorLog->vendor_id = $vendor->id;
            $sponsorLog->{"{$dataArr->type}_id"} = $item->id;
            $sponsorLog->action = 'add';
            $sponsorLog->save();

            // Commit Transaction
            \DB::commit();

            return apiResponse('success', ['sponsor_start_date' => $item->sponsor_start_date, 'sponsor_end_date' => $item->sponsor_end_date]);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postRemoveSponsorItem(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'type' => 'required|in:product,service',
            'id' => ('required|numeric|' . ($request->type == 'product' ? 'exists:products' : 'exists:services')),
        ]);
        $dataArr = arrayFromPost(['type', 'id']);

        try {
            $item = $dataArr->type == 'product' ? \App\Models\Product::find($request->id) : \App\Models\Service::find($request->id);
            if (!$item->is_sponsored) {
                return errorMessage("{$dataArr->type}_not_sponsored");
            }

            // Start Transaction
            \DB::beginTransaction();

            $item->is_sponsored = 0;
            $item->sponsor_start_date = null;
            $item->sponsor_end_date = null;
            $item->save();

            // Create Log
            $sponsorLog = new \App\Models\VendorSponsorItemLog();
            $sponsorLog->vendor_id = $vendor->id;
            $sponsorLog->{"{$dataArr->type}_id"} = $item->id;
            $sponsorLog->action = 'remove';
            $sponsorLog->save();

            // Commit Transaction
            \DB::commit();

            return apiResponse('success');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postPurchase(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'subscription_id' => 'required|exists:subscription_plans,id',
        ]);
        $dataArr = arrayFromPost(['subscription_id']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $subscription = \App\Models\SubscriptionPlan::find($dataArr->subscription_id);

            $paymentHistory = new \App\Models\VendorSubscriptionHistory();
            $paymentHistory->vendor_id = $vendor->id;
            $paymentHistory->subscription_plan_id = $subscription->id;
            $paymentHistory->amount = $subscription->amount;
            $paymentHistory->item_sponsor_duration = $subscription->item_sponsor_duration;
            $paymentHistory->total_items = $subscription->total_items;
            $paymentHistory->remaining_items = $subscription->total_items;
            $paymentHistory->payment_method = 1;
            $paymentHistory->payment_date = date('Y-m-d');
            $paymentHistory->transaction_id = generateRandomString(25);
            $paymentHistory->save();

            $response = \App\Helpers\NoonPayment::getInstance()->initiate([
                'amount' => number_format($paymentHistory->amount, 2, '.', ''),
                'item_name' => $subscription->en_name,
                'reference_id' => $paymentHistory->transaction_id,
                'locale' => $this->locale,
                'returnUrl' => route('payment.subscription.response'),
            ]);

            if ($response->err) {
                // Rollback Transaction
                \DB::rollBack();
                return errorMessage($response->message, true);
            }

            // Commit Transaction
            \DB::commit();

            return apiResponse('success', ['redirect_url' => $response->data->result->checkoutData->postUrl]);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }
}
