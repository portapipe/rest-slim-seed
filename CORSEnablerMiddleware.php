<?php

class CORSEnablerMiddleware extends \Slim\Middleware {
    public function call() {
        $this->app->response()->header('Access-Control-Allow-Origin', '*');
        $this->next->call();
    }
}
