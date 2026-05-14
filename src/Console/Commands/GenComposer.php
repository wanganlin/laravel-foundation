<?php

declare(strict_types=1);

namespace Juling\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenComposer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:composer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate composer.json description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->generate(dirname(base_path()) . '/infra', 'Juling');
        $this->generate(dirname(base_path()) . '/client', 'Juling\\Client');
    }

    private function generate(string $modulePath, string $namespace): void
    {
        $requires = [];
        $autoload = [
            'psr-4' => [],
            'files' => [],
        ];
        $providers = [];

        $composers = glob($modulePath . '/*/composer.json');
        foreach ($composers as $composer) {
            $componentDir = dirname($composer);
            $componentName = basename($componentDir);
            $componentStudlyName = Str::studly($componentName);

            $composerArr = json_decode(file_get_contents($composer), true);
            $requires = array_merge($requires, $composerArr['require']);

            // psr-4
            $autoload['psr-4'] = array_merge($autoload['psr-4'], [
                $namespace . '\\' . $componentStudlyName . '\\' => $componentName . '/src/',
            ]);

            // files
            if (file_exists($componentDir . '/src/helpers.php')) {
                $autoload['files'][] = $componentName . '/src/helpers.php';
            }
            if (file_exists($componentDir . '/src/Support/helpers.php')) {
                $autoload['files'][] = $componentName . '/src/Support/helpers.php';
            }

            // providers
            $serviceProviders = glob($componentDir . '/src/*ServiceProvider.php');
            if (isset($serviceProviders[0])) {
                $serviceProvider = basename($serviceProviders[0], '.php');
                $providers[] = $namespace . '\\' . $componentStudlyName . '\\' . $serviceProvider;
            }
        }

        ksort($requires);

        $composerFile = $modulePath . '/composer.json';
        $composerOut = json_decode(file_get_contents($composerFile), true);
        $composerOut['require'] = $requires;
        $composerOut['autoload'] = $autoload;

        if (!empty($providers)) {
            $composerOut['extra'] = [
                'laravel' => [
                    'providers' => $providers,
                ]
            ];
        }

        $composerContent = json_encode($composerOut, JSON_PRETTY_PRINT);
        $composerContent = str_replace('\/', '/', $composerContent);
        file_put_contents($composerFile, $composerContent);
    }
}
