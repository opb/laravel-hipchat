<?php

return [

    /*
     * Default options.
     * - 'apiToken' and 'room' are required
     * - All options apart from 'apiToken' can be overridden per message
     */

    'apiToken' => 'yourHipchatAPIKeyHere', // required

    'room' => 'Room Name', // room name (not ID), required

    'color' => 'yellow', // default notification color, optional

    'from' => 'MyApp', // default sender name, optional

    'queue' => true, // use laravel queue, optional, default true

    'notify' => false, // Hipchat app will notify room of the message

    'format' => 'auto', // message format - 'auto', 'html' or 'text', optional
];