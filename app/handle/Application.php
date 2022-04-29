<?php declare(strict_types=1);

namespace App\handle;

use App\code_template\swoft\SwoftInit;

/**
 * Class Application
 */
class Application
{
    public function run()
    {
        $SwoftInit = new SwoftInit();
        $SwoftInit->run();
    }
}
