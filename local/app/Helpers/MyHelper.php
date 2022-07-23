<?php

if (!function_exists('array_from_post')) {
    function arrayFromPost($fieldArr = [])
    {
        $request = request();
        $output = new \stdClass;
        if (count($fieldArr)) {
            foreach ($fieldArr as $value) {
                $output->$value = $request->input($value);
            }
        }
        return $output;
    }
}

if (!function_exists('transLang')) {
    function transLang($template = null, $dataArr = [])
    {
        return $template ? trans("messages.{$template}", $dataArr) : '';
    }
}

if (!function_exists('deleteFCMToken')) {
    function deleteFCMToken($device_id = false, $from = 'user')
    {
        if ($device_id) {
            \App\Models\FcmToken::where('device_id', $device_id)
                ->when($from == 'user', function ($query) {
                    $query->whereNotNull('user_id');
                })
                ->when($from != 'user', function ($query) {
                    $query->whereNotNull('vendor_id');
                })
                ->delete();

            return true;
        }
        return false;
    }
}

if (!function_exists('updateFCMToken')) {
    function updateFCMToken($dataArr = null, $column = 'user')
    {
        $fcmToken = new \stdClass;
        if ($dataArr == null) {
            return false;
        }

        $fcmToken = \App\Models\FcmToken::where('device_id', '=', $dataArr->device_id)
            ->where(function ($query) use ($column) {
                if ($column == 'user') {
                    $query->whereNull('vendor_id');
                } else {
                    $query->whereNull('user_id');
                }
            })
            ->first();
        if (blank($fcmToken)) {
            $fcmToken = new \App\Models\FcmToken();
        }

        $fcmToken->{"{$column}_id"} = $dataArr->{"{$column}_id"};
        $fcmToken->fcm_id = $dataArr->fcm_id;
        $fcmToken->device_id = $dataArr->device_id;
        $fcmToken->device_type = $dataArr->device_type;
        $fcmToken->save();

        return $fcmToken;
    }
}

if (!function_exists('generateOtp')) {
    function generateOtp()
    {
        return rand(1000, 9999);
    }
}

if (!function_exists('getLocales')) {
    function getLocales()
    {
        return ['en', 'ar'];
    }
}

if (!function_exists('processUserResponseData')) {
    function processUserResponseData($user = null, $access_token = null)
    {
        $output = new \stdClass;
        if (!blank($access_token)) {
            $output->access_token = $access_token;
            $output->token_type = 'bearer';
            $output->expires_in = (env('JWT_TTL') * 60);
            $output->expires_unit = 'Seconds';
        }

        if ($user != null) {
            unset($user->password, $user->remember_token, $user->otp, $user->otp_generated_at);

            if (!blank($access_token)) {
                $output->data = $user;
            } else {
                $output = $user;
            }
        }
        return $output;
    }
}

if (!function_exists('processVendorResponseData')) {
    function processVendorResponseData($vendor = null, $access_token = null)
    {
        $output = new \stdClass;
        if (!blank($access_token)) {
            $output->access_token = $access_token;
            $output->token_type = 'bearer';
            $output->expires_in = (env('JWT_TTL') * 60);
            $output->expires_unit = 'Seconds';
        }

        if ($vendor != null) {
            unset($vendor->password, $vendor->remember_token, $vendor->otp, $vendor->otp_generated_at);

            if (!blank($access_token)) {
                $output->data = $vendor;
            } else {
                $output = $vendor;
            }
        }
        return $output;
    }
}

if (!function_exists('successMessage')) {
    function successMessage($template = 'request_processed_successfully', $dataArr = null, $httpCode = 200)
    {
        $output = new \stdClass;
        $output->message = transLang($template);
        if ($dataArr != null) {
            $output->data = $dataArr;
        }
        return response()->json($output, $httpCode);
    }
}

if (!function_exists('exceptionErrorMessage')) {
    function exceptionErrorMessage($e, $throw_exception = false)
    {
        \Log::error($e);
        if (env('APP_DEBUG')) {
            return errorMessage($e->getMessage(), true, $throw_exception);
        }
        return errorMessage('session_expire', false, $throw_exception);
    }
}

if (!function_exists('errorMessage')) {
    function errorMessage($template = '', $string = false, $throw_exception = false)
    {
        $message = !$string ? transLang($template) : $template;

        if ($throw_exception) {
            $validator = \Validator::make([], []);
            $validator->errors()->add('error', $message);
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return response()->json([
            'message' => transLang('given_data_invalid'),
            'errors' => ['error' => [$message]],
        ], 422);
    }
}

if (!function_exists('generateFilename')) {
    function generateFilename()
    {
        return str_replace([' ', ':', '-'], '', \Carbon\Carbon::now()->toDateTimeString()) . generateRandomString(10);
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 6, $characters = 'upper_case,lower_case,numbers')
    {
        // $length - the length of the generated password
        // $count - number of passwords to be generated
        // $characters - types of characters to be used in the password

        // define variables used within the function
        $symbols = array();
        $passwords = array();
        $used_symbols = '';
        $pass = '';

        // an array of different character types
        $symbols['lower_case'] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols['upper_case'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols['numbers'] = '1234567890';
        $symbols['special_symbols'] = '!?~@#-_+<>[]{}';

        $characters = explode(',', $characters); // get characters types to be used for the password
        foreach ($characters as $key => $value) {
            $used_symbols .= $symbols[$value]; // build a string with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

        for ($p = 0; $p < 1; ++$p) {
            $pass = '';
            for ($i = 0; $i < $length; ++$i) {
                $n = rand(0, $symbols_length); // get a random character from the string with all characters
                $pass .= $used_symbols[$n]; // add the character to the password string
            }
            $passwords = $pass;
        }

        return $passwords; // return the generated password
    }
}

if (!function_exists('getTimeDiff')) {
    function getTimeDiff($input = '')
    {
        return $input ? \Carbon::createFromTimeStamp(strtotime($input))->diffForHumans() : '';
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($input = '', $to_format = 'Y-m-d H:i:s', $from_format = 'Y-m-d H:i:s')
    {
        return $input ? \Carbon::createFromFormat($from_format, $input)->format($to_format) : '';
    }
}

if (!function_exists('calculateVendorRating')) {
    function calculateRating($id, $type = 'product', $total_voters = true)
    {
        $voters = $rating = 0;
        $response = \DB::table("{$type}_reviews")
            ->select(\DB::raw('COUNT(*) AS total, SUM(rating) AS rating'))
            ->where("{$type}_id", $id)
            ->first();
        if (!blank($response)) {
            $voters = $response->total;
            $rating = $response->rating ? round($response->rating / $response->total, 1) : 0;
        }

        if ($total_voters) {
            return (object) ['voters' => $voters, 'rating' => $rating];
        }

        return $rating;
    }
}

if (!function_exists('filterMobileNo')) {
    function filterMobileNo($mobile = null, $dial_code = null)
    {
        if (!$mobile || !$dial_code) {
            return '';
        }

        $mobile = str_replace('+', '', $mobile);
        if (substr($mobile, 0, strlen($dial_code)) === $dial_code) {
            $mobile = substr($mobile, strlen($dial_code));
        } elseif (substr($mobile, 0, 1) == "0") {
            $mobile = substr($mobile, 1);
        }

        return $mobile;
    }
}

if (!function_exists('getTokenUser')) {
    function getTokenUser($force_login = true)
    {
        $request = request();
        $tokenUser = null;
        if ($request->header('Authorization')) {
            if (!$tokenUser = \JWTAuth::parseToken()->authenticate()) {
                throw new \Tymon\JWTAuth\Exceptions\TokenExpiredException();
            }
        }
        if ($tokenUser == null && $force_login) {
            throw new \Tymon\JWTAuth\Exceptions\TokenExpiredException();
        } elseif ($tokenUser != null && $tokenUser->status != 1) {
            throw new \Tymon\JWTAuth\Exceptions\TokenExpiredException();
        }
        return $tokenUser;
    }
}

if (!function_exists('sendEmail')) {
    function sendEmail($file, $subject, $to_email, $configArr = [])
    {
        if (blank($to_email)) {
            return false;
        }

        if (env('ALLOW_SEND_EMAIL')) {
            try {
                return \Mail::send("email_templates.en.{$file}", $configArr, function ($message) use ($to_email, $subject) {
                    $message->subject($subject)
                        ->to($to_email);
                });
            } catch (\Exception $e) {
                \Log::error($e);
                return $e->getMessage();
            }
        }
    }
}

if (!function_exists('generateBookingCode')) {
    function generateBookingCode()
    {
        return 'MJ' . getRandomNumber(4);
    }
}

if (!function_exists('getRandomNumber')) {
    function getRandomNumber($digits = 6)
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }
}

if (!function_exists('getAppSetting')) {
    function getAppSetting($attribute = false)
    {
        if (!$attribute) {
            return null;
        }

        $setting = \App\Models\Setting::select('value')->where('attribute', $attribute)->first();
        return $setting != null ? $setting->value : null;
    }
}

if (!function_exists('getDaysBetweenDates')) {
    function getDaysBetweenDates($startDate, $endDate, $format = 'Y-m-d')
    {
        $response = [];
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $response[] = $date->format($format);
        }
        return $response;
    }
}

if (!function_exists('strpos_arr')) {
    function strpos_arr($haystack, $needle)
    {
        $response = [];
        $needle = !is_array($needle) ? [$needle] : $needle;

        foreach ($needle as $key => $what) {
            if (($pos = strpos($what, $haystack)) !== false) {
                $response[] = $key;
            }

        }
        return $response;
    }
}

if (!function_exists('setJWTSettings')) {
    function setJWTSettings()
    {
        // Change default guard
        \Config::set('auth.defaults', [
            'guard' => 'vendor',
            'passwords' => 'vendor',
        ]);
    }
}

if (!function_exists('compareNumbers')) {
    function compareNumbers($number1, $number2)
    {
        if ($number2) {
            return (abs(($number1 - $number2) / $number2) < 0.00001);
        }
        return ((double) $number1 == (double) $number2);
    }
}

if (!function_exists('getSessionLang')) {
    function getSessionLang($session = 'admin')
    {
        $keyArr = ['admin' => 'lang'];
        return \Session::get($keyArr[$session]) ? \Session::get($keyArr[$session]) : env('APP_LOCALE');
    }
}

if (!function_exists('getCustomSessionLang')) {
    function getCustomSessionLang($locale = null)
    {
        $locale = is_null($locale) ? getSessionLang() : $locale;
        return $locale == 'ar' ? '' : "{$locale}_";
    }
}

if (!function_exists('buildHierarchyTree')) {
    function buildHierarchyTree($elements, $parentId = null)
    {
        $branch = collect();

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = buildHierarchyTree($elements, $element->id);
                if ($children->count()) {
                    $element->children = $children;
                }
                $branch->add($element);
            }
        }

        return $branch;
    }
}

if (!function_exists('apiResponse')) {
    function apiResponse($template = 'success', $dataArr = null, $httpCode = 200)
    {
        $output = new \stdClass;
        $output->message = transLang($template);
        !$dataArr || $output->data = $dataArr;
        return response()->json($output, $httpCode);
    }
}

if (!function_exists('decodeUnicodeString')) {
    function decodeUnicodeString($string)
    {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $string);
    }
}

if (!function_exists('storeDebugLogs')) {
    function storeDebugLogs($dataArr = null, $type = 'info', $channel = 'daily')
    {
        try {
            if (is_array($dataArr)) {
                $firstParam = isset($dataArr[0]) ? $dataArr[0] : '';
                $secondParam = isset($dataArr[1]) ? $dataArr[1] : [];

                \Log::channel($channel)->$type($firstParam, $secondParam);
            } else {
                $firstParam = $dataArr;
                \Log::channel($channel)->$type($dataArr);
            }
        } catch (\Throwable $th) {
            try {
                \Log::channel($channel)->$type($firstParam);
                \Log::channel($channel)->$type($secondParam);
            } catch (\Throwable $th) {}
        }
    }
}

// $type: days / hours / Obj
if (!function_exists('getBtwDays')) {
    function getBtwDays($from_date = null, $to_date = null, $type = 'days')
    {
        if (!$from_date || !$to_date) {
            return 0;
        }

        $from_date = \Carbon\Carbon::parse($from_date);
        $to_date = \Carbon\Carbon::parse($to_date);
        $interval = $from_date->diff($to_date);
        if ($type == 'object') {
            $dateObj = new \stdClass;
            $dateObj->months = $interval->format('%m') + ($interval->y * 12);
            $dateObj->days = $interval->d;
            $dateObj->hours = (int) number_format(ceil(($interval->h * 60 + ($interval->i >= 30 ? 1 : 0)) / 60), 0);
            $dateObj->total_days = $interval->days;

            if ($dateObj->months == 0 && $dateObj->days == 0 && $dateObj->hours == 0) {
                $dateObj->hours = 1;
            }

            return $dateObj;
        } elseif ($type == 'days') {
            return (int) $from_date->diff($to_date)->format('%a');
        } else {
            return (int) number_format(ceil(($interval->h * 60 + ($interval->i >= 30 ? 1 : 0)) / 60), 0);
        }
    }
}

if (!function_exists('bookingCalculation')) {
    function bookingCalculation($from, $to, $amount, $amount_type, $extra_charges = 0, $security_amount = 0, $tax_percentage = null)
    {
        $dateObj = getBtwDays($from, $to, 'object');
        $monthDays = date('t', strtotime($to));

        //amount_type: 1.Hourly, 2.Daily, 3.Monthly
        if ($amount_type == 1) {
            $subtotal = ($dateObj->total_days * 24 + $dateObj->hours) * $amount;
            // $subtotal = ($dateObj->months * $monthDays * 24 + $dateObj->days * 24 + $dateObj->hours) * $amount;
        } elseif ($amount_type == 2) {
            $subtotal = ($dateObj->total_days + $dateObj->hours / 24) * $amount;
            // $subtotal = ($dateObj->months * $monthDays + $dateObj->days + $dateObj->hours / 24) * $amount;
        } elseif ($amount_type == 3) {
            $subtotal = ($dateObj->months + $dateObj->days / $monthDays + $dateObj->hours / 24 / $monthDays) * $amount;
        }
        $subtotal = round($subtotal, 2);

        $tax_percentage = is_null($tax_percentage) ? getAppSetting('tax_percentage') : $tax_percentage;

        $subtotalAmt = $subtotal;
        $subtotalAmt += $extra_charges;

        $tax_amount = (string) round($subtotalAmt * $tax_percentage * 0.01, 2);
        $total_amount = (string) ($subtotalAmt + $tax_amount + $security_amount);
        $subtotal = (string) $subtotal;

        $days = $dateObj->total_days;
        $hours = $dateObj->hours;
        $delivery_charges = (string) $extra_charges;

        return compact('subtotal', 'delivery_charges', 'tax_amount', 'total_amount', 'days', 'hours');
    }
}

if (!function_exists('calculateDelayCharges')) {
    function calculateDelayCharges($from, $to, $amount, $amount_type)
    {
        $dateObj = getBtwDays($from, $to, 'object');
        $monthDays = date('t', strtotime($to));

        //amount_type: 1.Hourly, 2.Daily, 3.Monthly
        $subtotal = 0;
        if ($amount_type == 1) {
            $subtotal = ($dateObj->total_days * 24 + $dateObj->hours) * $amount;
            // $subtotal = ($dateObj->months * $monthDays * 24 + $dateObj->days * 24 + $dateObj->hours) * $amount;
        } elseif ($amount_type == 2) {
            $subtotal = ($dateObj->total_days + $dateObj->hours / 24) * $amount;
            // $subtotal = ($dateObj->months * $monthDays + $dateObj->days + $dateObj->hours / 24) * $amount;
        } elseif ($amount_type == 3) {
            $subtotal = ($dateObj->months + $dateObj->days / $monthDays + $dateObj->hours / 24 / $monthDays) * $amount;
        }

        $subtotal = round($subtotal, 2);
        $days = $dateObj->total_days;
        $hours = $dateObj->hours;
        $total_hours = $dateObj->total_days * 24 + $dateObj->hours;

        return compact('subtotal', 'days', 'hours', 'total_hours');
    }
}

if (!function_exists('numberFormatShort')) {
    function numberFormatShort($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $n_format . $suffix;
    }
}