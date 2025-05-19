<?php

namespace App\Config;

enum CourseResourceType: string
{
    case OTHER = 'other';
    case EXERCISE = 'exercise';
    case TP = 'tp';
}
