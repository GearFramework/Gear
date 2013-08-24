Gear
====

PHP Framework (PHP 5.3 or higher)

====

#### Установка

Так как фреймворк задумывался также под возможность однорвеменного использования несколькими проектами, то выбирать папку для установки следует скорее всего в /usr/share/gear/gear. В данную папку распаковываем исходники фреймворка.
Подобное расположение обусловлено тем, что /usr/share/gear будет являться контейнером для будущих проектов, т.е. структура папок должна выглядеть:

/ -+
   |
   +- usr
       |
       +- share
           |
           +- gear
               |
               +- gear
               |    |
               |    +- behaviors
               |    +- builder
               |    +- components
               |    +- ... и т.д.
               |
               +- firstproject
               |    |
               |    +- behaviors
               |    +- components
               |    +- ... и т.д.
               |    +- Firstproject.php
               |
               +- secondproject
               |    |
               |    +- behaviors
               |    +- components
               |    +- ... и т.д.
               |    +- Secondproject.php
               |
               +- ... и т.д.