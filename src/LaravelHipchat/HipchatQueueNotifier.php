<?php namespace Opb\LaravelHipchat;

use Illuminate\Foundation\Application as App;

class HipchatQueueNotifier
{
    /**
     * @var HipchatNotifier
     */
    protected $notifier;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->notifier = $app->make('hipchat-notifier');
    }

    /**
     * Process the queued message
     *
     * @param $job
     * @param array $data
     * @return boolean
     */
    public function fire($job, array $data)
    {
        $message = $data['message'];
        $options = $data['options'];
        $options['queue'] = false;

        if($status = $this->notifier->message($message, $options))
        {
            $job->delete();
            return true;
        }

        return false;
    }
} 