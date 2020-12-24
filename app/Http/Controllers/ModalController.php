<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;

class ModalController extends Controller
{
    function show($type, $itemId = null) {
        $view = view("modals.${type}");

        if($type == "statuslabel") {
            $view->with('statuslabel_types', Helper::statusTypeList());
        }
        return $view;
    }
}
