<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function getMonthlyExirationDate() {
        $newDateTime = Carbon::now()->addMonth();

        return response(["date" => $newDateTime]);
    }

    public function getYearlyExirationDate() {
        $newDateTime = Carbon::now()->addYear();

        return response(["date" => $newDateTime]);
    }

    public function today() {
        return response(["date" => Carbon::now()]);
    }
}
