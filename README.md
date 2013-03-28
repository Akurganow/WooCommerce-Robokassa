Robokassa for WooCommerce
========

После активации плагина через панель управления в WooCommerce прописывем
Логин мерчат, пароль 1, пароль 2 узнать их можно в [личном кабинете robokassa](https://www.roboxchange.com/Environment/Partners/Login/Merchant/Registration.aspx)


В Robokassa прописываем:
* Result URL: http://your_domain/?wc-api=wc_robokassa&robokassa=result
* Success URL: http://your_domain/?wc-api=wc_robokassa&robokassa=success
* Fail URL: http://your_domain/woocommerce/?wc-api=wc_robokassa&robokassa=fail
* Метод отсылки данных: POST


Более подробно на [странице плагина](http://polzo.ru/wc-robokassa)


Installation
----------

1. Убедитесь что у вас установлена посленяя версия плагина <a href="//www.woothemes.com/woocommerce" title="WooCommerce">WooCommerce</a>
2. Распакуйте архив и загрузите "robokassa-for-woocommerce" в папку ваш-домен/wp-content/plugins
3. Активируйте плагин
