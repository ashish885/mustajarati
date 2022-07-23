<?php

if (!function_exists('getGroupNavigation')) {
    function getGroupNavigation()
    {
        $locale = getSessionLang() == 'en' ? 'en_' : '';
        $navigation = \App\Models\AdminNavigation::select(DB::raw("id, {$locale}name AS name, parent_id"))
            ->where('status', 1)
            ->where('show_in_permission', 1)
            ->orderBy("display_order")
            // ->orderBy("{$locale}name")
            ->get()
            ->toArray();

        return count($navigation) ? arrayToTree($navigation, null) : $navigation;
    }
}

if (!function_exists('getRolePermission')) {
    function getRolePermission($accessRoleId)
    {
        return \App\Models\AdminRolePermission::where('admin_role_id', $accessRoleId)
            ->pluck('admin_navigation_id')
            ->toArray();
    }
}

if (!function_exists('getAdminPermission')) {
    function getAdminPermission($accessAdminId)
    {
        return \App\Models\AdminPermission::where('user_id', $accessAdminId)
            ->pluck('admin_navigation_id')
            ->toArray();
    }
}

if (!function_exists('getAdminPermissionIDs')) {
    function getAdminPermissionIDs($accessAdminId, $accessRoleId)
    {
        $usersPermissions = []; //getAdminPermission($accessAdminId);
        return count($usersPermissions) ? $usersPermissions : getRolePermission($accessRoleId);
    }
}

if (!function_exists('arrayToTree')) {
    function arrayToTree($elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = arrayToTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}

if (!function_exists('hasAccess')) {
    function hasAccess($actionPath, $exclude = false)
    {
        if ($exclude === true) {
            return true;
        }

        if (\Session::has('navigationPermissions')) {
            $navigationPermissions = \Session::get('navigationPermissions');
            $key = array_search($actionPath, array_column($navigationPermissions, 'action_path'));
            return $key !== false ? true : false;
        }
        return false;
    }
}

// Set Navigation in Session
if (!function_exists('navigationMenuListing')) {
    function navigationMenuListing($guard = 'admin', $saveSession = true, $accessAdminId = null, $accessRoleId = null)
    {
        $excludeRoleId = [1];
        $navigationMasters = [];

        if ($saveSession == true) {
            $guardData = \Auth::guard($guard)->user();
            $accessAdminId = $guardData->id;
            $accessRoleId = $guardData->admin_role_id;
        }

        if (in_array($accessRoleId, $excludeRoleId)) {
            $navigationMasters = \App\Models\AdminNavigation::select(DB::raw('id, name, en_name, icon, parent_id, action_path, show_in_menu'))
                ->orderBy('display_order', 'ASC')
                ->where('status', 1)
                ->get();
        } else {
            $allowedNavIds = getAdminPermissionIDs($accessAdminId, $accessRoleId);
            if (count($allowedNavIds)) {
                $navigationMasters = \App\Models\AdminNavigation::select(DB::raw('id, name, en_name, icon, parent_id, action_path, show_in_menu'))
                    ->orderBy('display_order', 'ASC')
                    ->whereIn('id', $allowedNavIds)
                    ->where('status', 1)
                    ->get();
            }
        }

        if (count($navigationMasters)) {
            $navigation = arrayToTree($navigationMasters->where('show_in_menu', 1)->toArray(), null);

            if ($saveSession == true) {
                \Session::put("navigation_{$guard}", $navigation);
                \Session::put("navigation_permission_{$guard}", $navigationMasters->toArray());
            }
        }

        return $saveSession === true ? $navigationMasters : true;
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission()
    {
        $stringArr = func_get_args();
        if (blank(\Session::get('navigation_permission_admin'))) {
            navigationMenuListing();
        }

        if (count($stringArr) > 1) {
            foreach ($stringArr as $string) {
                $string_found = array_search($string, array_column(\Session::get('navigation_permission_admin'), 'action_path'));
                if ($string_found !== false) {
                    return true;
                }
            }
        } else {
            $string_found = array_search($stringArr[0], array_column(\Session::get('navigation_permission_admin'), 'action_path'));
        }
        return $string_found === false ? false : true;
    }
}
