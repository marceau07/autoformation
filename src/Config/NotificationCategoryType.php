<?php

namespace App\Config;

enum NotificationCategoryType: string
{
    case HOMEWORK_TO_DO = 'homework_to_do';
    case NEW_COURSE = 'new_course';
    case NEW_FEATURES = 'new_features';
    case NEW_INTERNSHIP = 'new_internship';
    case NEW_MESSAGE = 'new_message';
}
