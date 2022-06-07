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
        $host = $_SERVER["REQUEST_URI"];
        $host = explode('/', $host);
        if(in_array('index', $host)){
            // /src/code_generate_front/dist/
            $viewUrl = BASE_PATH . 'src/code_generate_front/dist/index.html';
            $content = file_get_contents($viewUrl);
            echo $content;
            die();
        }
        if(in_array('run', $host)){
            $SwoftInit = new SwoftInit();
            $SwoftInit->run();
        }
    }
}
