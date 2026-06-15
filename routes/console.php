<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Console\Scheduling\Schedule;

Schedule::command('inspire')
    ->hourly();
