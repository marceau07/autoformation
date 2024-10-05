<?php 

namespace App\Config;

enum QuizType: string
{
    case DEFAULT = 'default';
    case UNIQUE_CHOICE = 'unique_choice';
    case MULTIPLE_CHOICE = 'multiple_choice';
    case TRUE_FALSE = 'true_false';
    case SHORT_ANSWER = 'short_answer';
    case LONG_ANSWER = 'long_answer';
}