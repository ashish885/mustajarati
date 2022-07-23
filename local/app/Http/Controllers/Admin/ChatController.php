<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ChatController extends AdminController
{
    public function chat()
    {
        abort_unless(hasPermission('admin.chats.chat'), 401);

        return view('admin.chats.chat');
    }
}
