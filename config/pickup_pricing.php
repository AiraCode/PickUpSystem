<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Initial Fee (Rupiah)
    |--------------------------------------------------------------------------
    | Fixed base charge applied to every courier pickup regardless of distance.
    */
    'initial_fee' => 5000,

    /*
    |--------------------------------------------------------------------------
    | Distance Rate (Rupiah per km)
    |--------------------------------------------------------------------------
    | Cost multiplied by the route distance in kilometers.
    */
    'distance_rate' => 2300,

    /*
    |--------------------------------------------------------------------------
    | Time Rate (Rupiah per second)
    |--------------------------------------------------------------------------
    | Cost multiplied by the calculated travel time in seconds.
    */
    'time_rate' => 25,

    /*
    |--------------------------------------------------------------------------
    | Courier Average Speed (km/h)
    |--------------------------------------------------------------------------
    | Used to internally derive travel time from route distance.
    | Not exposed to users or administrators.
    */
    'courier_speed_kmh' => 40,

    /*
    |--------------------------------------------------------------------------
    | Default Pricing Multiplier
    |--------------------------------------------------------------------------
    | Applied to the base price. Set to 1.00 until dynamic factors are active.
    | Future integrations: weather, traffic, demand, events, holidays.
    */
    'default_multiplier' => 1.00,

];
