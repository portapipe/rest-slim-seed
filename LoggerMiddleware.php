<?php

class LoggerMiddleware extends \Slim\Middleware {

    public function call() {
        $this->app->log->setEnabled(true);
        $this->app->log->debug($this->app->request()->getResourceUri());
        $this->next->call();
    }

}
