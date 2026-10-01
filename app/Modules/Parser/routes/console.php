<?php

//Обновляем данные по товарам уже спарсенным
//MAINDO Запустить ежедневный парсинг остатков и цены
Schedule::command('ikea:products-update')->dailyAt('01:01');//dailyAt('02:01');
