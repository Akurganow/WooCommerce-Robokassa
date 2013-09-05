Robokassa
========

Плагин для WooCommerce добавляющий в платежные шлюзы робокассу.

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

your_domain замените на адрес вашего сайта, например redwood-store.ru