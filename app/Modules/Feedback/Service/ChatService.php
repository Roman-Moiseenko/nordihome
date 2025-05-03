<?php

namespace App\Modules\Feedback\Service;

use App\Modules\Feedback\Entity\Chat;
use App\Modules\Feedback\Entity\Message;

class ChatService
{

    /** Получаем данные по сообщению из web-hooks */
    public function newMessage(array $params)
    {
        //по id клиента ищем чат
        //Если не найден, создаем
        //Добавляем сообщение

    }

    public function newAnswer(array $params)
    {
        $chat = Chat::find($params['chatId']);
        $chat->messages()->save(Message::new($params['message'], $params['staff_id']));

        //TODO Отправить сообщение по API для выбранной соц.сети
    }
}
