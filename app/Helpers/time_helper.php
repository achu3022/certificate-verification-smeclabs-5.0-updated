<?php

if (!function_exists('time_ago')) {
    function time_ago($datetime)
    {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;
        
        if ($difference < 60) {
            return 'Just now';
        }
        
        $periods = [
            'year'   => 31536000,
            'month'  => 2592000,
            'week'   => 604800,
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60
        ];
        
        foreach ($periods as $name => $seconds) {
            $quotient = floor($difference / $seconds);
            if ($quotient > 0) {
                if ($quotient == 1) {
                    return "1 {$name} ago";
                }
                return "{$quotient} {$name}s ago";
            }
        }
        
        return 'Just now';
    }
}