<?php

//Обновляем данные по товарам уже спарсенным
Schedule::command('cron:parser-update')->dailyAt('01:01');//dailyAt('02:01');
