Robokassa
========

Плагин для WooCommerce добавляющий в платежные шлюзы робокассу.

![TODOs by CodeNotes](http://codenotes.me/badge/4624048/todos.png)

Установка
----------

### Первый способ

1. Убедитесь что у вас установлена посленяя версия плагина <a href="//www.woothemes.com/woocommerce" title="WooCommerce">WooCommerce</a>
2. Распакуйте архив и загрузите "robokassa-for-woocommerce" в папку ваш-домен/wp-content/plugins
3. Активируйте плагин

### Второй способ

Установить из официального репозитория WordPress из админки вашего сайта.

Настройка
----------

После активации плагина через панель управления, в настройках WooCommerce, в платежных шлюзах ищем Робокассу и прописывем
Логин мерчат, пароль 1, пароль 2 узнать их можно в [личном кабинете robokassa](https://www.roboxchange.com/Environment/Partners/Login/Merchant/Registration.aspx)


В Robokassa прописываем:
* Result URL: http://your_domain/?wc-api=wc_robokassa&robokassa=result
* Success URL: http://your_domain/?wc-api=wc_robokassa&robokassa=success
* Fail URL: http://your_domain/?wc-api=wc_robokassa&robokassa=fail
* Метод отсылки данных: POST

<small style="color:red;">your_domain замените на адрес вашего сайта, например redwood-store.ru</small>

### Changelog
##### 0.9
* [Совместимость и WooCommerce 2.1.2](https://github.com/Akurganow/WooCommerce-Robokassa/issues/2)

##### 0.8
* Совместимость с WooCommerce 2

##### 0.7
* Добавлены поля description и instructions спасибо пользователю <a href="https://twitter.com/vladsg" target="_blank">@vladsg</a>

##### 0.6
* Сняты ограничения бесплатной версии, плагин теперь бесплатен

##### 0.5
* Несколько мелких нововведений
* И еще немного мелких исправлений

##### 0.4.1
* Еще немного мелких исправлений

##### 0.4
* Другие мелкие исправления

##### 0.3
* Решена проблема с конфликтующими функциями
* Другие мелкие исправления

##### 0.2.1
* Решена проблема с отображением логотипа робокассы

##### 0.2
* Решена проблема с обновлением данных в БД

##### 0.1.2
* Исправления ошибок

##### 0.1
* Релиз плагина
