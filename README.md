# sviatoslavtesttask
### Hi there 👋, Плагин для подключения Tegro API к WooCommerce
#### Этот плагин был написан на базе PHP 7.0+ и Python 3.6
Для подключение необходимо скачать папку с плагином и поместить ее по следущему пути wp_coontent/plugins/имя папки.
Из папки необходимо извлечь файл tegro-plugin.py и поместить по пути wp-content/plugins/tegro-plugin.py.
В файле woocommerce_tegro_plugin.php необходимо изменить следующие данные.
На 67 строчке поставте свой shop id.
На 85 строчке замените на путь к вашему python файлу.
В файле tegro-plugin.py необходимо замеенить следующие данные.
На 19 строчке secret key.
Далее для подключения зайдите в Wordpress в раздел плагина и включите плагин под названием WooCommerce Tegro Plugin.
Поздравляю. Плагин готов к использованию.




