<?php

$isDevelopment = in_array(env('APP_ENV', 'production'), ['local', 'testing'], true);

return [
    'roster' => env('FEATURE_ROSTER_ENABLED', $isDevelopment),
    'attendance' => env('FEATURE_ATTENDANCE_ENABLED', $isDevelopment),
    'class_calendar' => env('FEATURE_CLASS_CALENDAR_ENABLED', $isDevelopment),
    'daily_updates' => env('FEATURE_DAILY_UPDATES_ENABLED', $isDevelopment),
    'messaging' => env('FEATURE_MESSAGING_ENABLED', $isDevelopment),
    'family_portal' => env('FEATURE_FAMILY_PORTAL_ENABLED', $isDevelopment),
];
