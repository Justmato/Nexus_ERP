<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('inventory', function ($user) {
    return $user !== null;
});
