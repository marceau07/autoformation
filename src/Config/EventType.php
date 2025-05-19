<?php

namespace App\Config;

enum EventType: string
{
    case VACATION = 'vacation';
    case WORK_STOPAGE = 'work_stopage';
    case ASYNCHRONOUS_MODULE = 'asynchronous_module';
    case SYNCHRONOUS_MODULE = 'synchronous_module';
}
