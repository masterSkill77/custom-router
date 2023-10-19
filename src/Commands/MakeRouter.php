<?php

namespace Masterskill\CustomRouter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRouter extends Command
{
    /**
     * @param string name
     * @prefix string the prefix of the router
     */
    protected $signature = 'make:router';
    /**
     * Description of the command
     */
    protected $description = "It's a package that can create new route on Laravel, based on its parameters. Like we have api, web, we can now easily configure this with custom-router";

    public function handle()
    {
        $filename = $this->ask("What is the name of the router ?");
        $prefix = $this->ask("What is the prefix of the router");
        $middleware = $this->choice(
            'What middleware do you want to apply ?',
            ['api', 'web'],
            0
        );

        $this->createFile($filename, $prefix, $middleware);
    }

    /**
     * Create the file on the disk
     * @param string $filename
     */
    protected function createFile(string $filename, string $prefix, string $middleware)
    {
        // Content of the file, with a test endpoint directly generated
        $content = "
        <?php

        use Illuminate\Support\Facades\Route;

        /*
        |--------------------------------------------------------------------------
        | API Routes
        |--------------------------------------------------------------------------
        |
        | Here is where you can register API routes for your application.
        |
        */

        Route::get('test', function(){
            return response()->json('It works');
        });
        ";

        $filePath = base_path('routes/') . $filename . '.php';
        if (file_put_contents($filePath, $content)) {
            $this->info("The router $filePath has been created successfully.");

            $this->addToRouteServiceProvider($filePath, $prefix, $middleware);

            $this->info("Enjoy your development :)");
        } else {
            $this->error("An error occurred while creating the router.");
        }
    }

    protected function addToRouteServiceProvider(string $className, string $prefix, string $middleware)
    {
        // Get the content of RouteServiceProvider
        $routeServiceProviderPath = app_path('Providers/RouteServiceProvider.php');
        $routeServiceProviderContent = File::get($routeServiceProviderPath);

        // Define the route binding
        $binding = "
        Route::middleware('web')
            ->namespace(\$this->namespace)
            ->prefix('$prefix')
            ->group(base_path('routes/$className.php'));
        ";

        $position = strpos($routeServiceProviderContent, "(base_path('routes/web.php'));");

        if ($position !== false) {
            // Insert the binding just before the class definition
            $routeServiceProviderContent = substr_replace($routeServiceProviderContent, $binding, $position, 0);
        }

        // Save the modified content back to the RouteServiceProvider
        File::put($routeServiceProviderPath, $routeServiceProviderContent);
    }
}
