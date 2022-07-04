<?php

require './Bot.php';

use ArashAbedii\Bot;

$token='';

$bot=Bot::sendCustomRequest($token,'sendMessage',array(
    'chat_id'=>'',
    'text'=>'Hello World!'
));


