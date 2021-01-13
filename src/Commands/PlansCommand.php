<?php

namespace Abr4xas\Plans\Commands;

use Illuminate\Console\Command;

class PlansCommand extends Command
{
    public $signature = 'plans';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
