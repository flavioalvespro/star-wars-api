<?php

use App\Jobs\ComputeSearchStatistics;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ComputeSearchStatistics())
    ->everyFiveMinutes()
    ->onOneServer();
