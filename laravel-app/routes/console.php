<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('cinevo:about', function (): void {
    $this->info('Cinevo Laravel conversion: authentication and database module installed.');
});
