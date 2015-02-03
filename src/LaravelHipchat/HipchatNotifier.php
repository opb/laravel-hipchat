<?php namespace Opb\LaravelHipchat;

use HipChat\HipChat;
use HipChat\HipChat_Exception;
use Illuminate\Queue\QueueManager as Queue;

/**
 * Class HipchatNotifier
 *
 * Send messages via the HipChat API
 *
 * @package Opb\LaravelHipchat
 */
class HipchatNotifier
{
    /**
     * @var HipChat
     */
    protected $hipchat;

    /**
     *
     * @var Queue
     */
    protected $queue;

    /**
     * class-wide options, used if not overridden
     * when a message is sent
     *
     * @var array
     */
    protected $options;

    /**
     * Available message colors
     *
     * @var array
     */
    protected $colors = [
        'yellow',
        'red',
        'gray',
        'green',
        'purple',
        'random',
    ];

    /**
     * Default options if none are specified
     *
     * @var array
     */
    protected $defaults = [
        'queue' => true,
        'color' => 'yellow',
        'notify' => false,
        'format' => 'auto',
        'from' => 'Notification',
    ];

    /**
     * Options available in the $options array:
     * 'queue' - boolean, default true, process this message via laravel queue
     * 'color' - string, default 'yellow', message color
     * 'notify' - boolean, default false, trigger notification in HipChat app
     * 'format' - string, default 'auto', text format of the message
     * 'from' - string, default 'Notification', the name of the sender of this message
     *
     * Any of these options can be overridden by respecifying them when
     * sending a message
     *
     * @param HipChat $hipchat
     * @param Queue $queue
     * @param array $options default options which can be overridden per message
     * @param $room name of the destination room of the message
     */
    public function __construct(HipChat $hipchat, Queue $queue, array $options = [], $room)
    {
        $this->hipchat = $hipchat;
        $this->queue = $queue;
        $this->options = array_merge($this->defaults, $options);
        $this->options['room'] = $room;
    }

    /**
     * The main function of this class - send a message
     *
     *
     * @param string $message
     * @param array $options Same options as class constructor
     * @param string $room name of the destination room of the message
     * @return bool|mixed
     * @throws HipchatNotifierException
     */
    public function message($message, array $options = [], $room = null)
    {
        $options = array_merge($this->options, $options);
        if($room) $options['room'] = $room;

        if(!in_array($options['color'], $this->colors))
        {
            $msg = "Selected color [{$options['color']}] is not in list of available colors. Please refer to docs.";
            throw new HipchatNotifierException($msg);
        }

        if($options['queue'])
            return $this->queue($message, $options);

        $format = $options['format'] === 'auto' ? $this->getFormat($message) : $options['format'];

        return $this->hipchat->message_room(
            $options['room'],
            $options['from'],
            $message,
            $options['notify'],
            $options['color'],
            $format
        );
    }

    /**
     * Send the message and options to HipchatQueueNotifier
     * so that we're not trying to process the API call to
     * HipChat synchronously.
     *
     * @param string $message
     * @param array $options Same options as class constructor
     * @return mixed
     */
    protected function queue($message, array $options)
    {
        $data = [
            'message' => $message,
            'options' => $options,
        ];

        return $this->queue->push(HipchatQueueNotifier::class, $data);
    }

    /**
     * Check for presence of HTML tags in message, to determine
     * whether format of the message is 'html' or 'text'
     *
     * @param string $msg
     * @return string
     */
    protected function getFormat($msg)
    {
        $stripped = strlen(strip_tags($msg));
        return (strlen($msg) == $stripped) ? 'text' : 'html';
    }

    /**
     * Get the HipChat instance used by the notifier
     *
     * @return HipChat
     */
    public function hipchat()
    {
        return $this->hipchat;
    }
}