<?php

namespace Masterskill\CustomRouter;

use Illuminate\Support\ServiceProvider;
use Masterskill\CustomRouter\Commands\MakeRouter;

class MakeRouterProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeRouter::class
        ]);
    }
}
