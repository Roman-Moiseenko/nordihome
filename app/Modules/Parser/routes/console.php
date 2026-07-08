<?php

//Обновляем данные по товарам уже спарсенным
Schedule::command('ikea:products-update')->dailyAt('01:01');//dailyAt('02:01');
