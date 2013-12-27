Gear Framework
==============

Open Source PHP Framework (PHP 5.3 or higher)

Установка
---------

Так как фреймворк задумывался также под возможность одновременного использования несколькими проектами, то выбирать папку для установки следует скорее всего в /usr/share/gear/gear. В данную папку распаковываем исходники фреймворка.
Подобное расположение обусловлено тем, что /usr/share/gear будет являться контейнером для будущих проектов, т.е. структура папок должна выглядеть:

```
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
               +- testProject
               |    |
               |    +- behaviors
               |    +- components
               |    +- ... и т.д.
               |    +- TestProject.php
               |
               +- otherProject
               |    |
               |    +- behaviors
               |    +- components
               |    +- ... и т.д.
               |    +- OtherProject.php
               |
               +- ... и т.д.
```
Где
`/usr/share/gear/testProject` и `/usr/share/gear/otherProject` - папки с исходными кодами проектов, расположенных и работающих на данном сервере.

Пространство имён
-----------------

Создание проекта
----------------

Все запросы должны проходить через единственный файл index.php, который в общем случае, располагается в `DOCUMENT_ROOT` вашего Apache-сервера (допустим это будет `/var/www`). Помимо index.php рядом могут располагаться конфигурационные файлы `config.production.php` и/или `config.debug.php`, в зависимости от режима работы приложения (config.production.php - настройки приложения для работы в продакшене, config.debug.php - настройки приолжения для работы в режиме разработки и отладки), но также возможно использовать любой иной файл-конфигурации (при инициализации ядра необходимо указать путь к данному файлу).
В минимальном виде index.php содержит три строчки кода:

```
<?php
require '/usr/share/gear/gear/Core.php';
\gear\Core::init();
\gear\Core::app()->run();
```
Первой строкой подключается ядро фреймворка. Далее следует инициализация ядра посредством вызова метода `init()`. Процесс инициализации представляет собой чтение конфигурационного файла (если таковой имеется), и исходя из его настроек - подключение необходимых библиотек, модулей, компонентов.
Метод `\gear\Core::init()` имеет два необязательных параметра. Первый, собственно, определяет конфигурацию, второй устанавливает режим работы приложения: разработка/отладка(определяется константой `\gear\Core::MODE_DEVELOPMENT`), рабочая версия (определяется константой `\gear\Core::MODE_PRODUCTION`). По-умоланию, второй параметр имеет значение `\gear\Core::MODE_DEVELOPMENT`.
В зависимости от значения первого параметра процесс инициализации может происходить несколькими путями:

Первый параметр не указан. В этом случае, метод `\gear\Core::init()` попытается найти конфигурационный файл в папке, где располагается непосредственно `index.php`, в зависимости от режима работы приложения это может быть либо config.debug.php, либо config.production.php, если таковые не найдены, то генерируется исключение `\gear\CoreException`.
Непосредственно массив с настройками
```
\gear\Core::init(array
(
    'modules' => array
    (
        'app' => array('class' => '\\myproject\\MyProject'),
    ),
));
```
```
\gear\Core::init(require('/var/www/config.myproject.php'));
```
 

Модули
------

Самостоятельная сущность, у которой могут быть свои компоненты, бибилиотеки, шаблоны отображения и т.д. Добавление модулей происходит в конфигурационном файле в разеде `modules`.

Компоненты
----------

Плагины
-------

Поведения
---------

ORM
---

#### Выборка

Самая простая выборка, соответствующая SQL-запросу `SELECT * FROM products`:

```
$collection = \gear\Core::c('db')->selectCollection('database', 'products');
foreach($collection as $itemProduct)
    echo $itemProduct->name, '<br />';
```
Условие выборки:
 
```
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 3));
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```

> Любые `SELECT` запросы являются отложенными до вызова одного из методов полученного курсора:

```
$cursor->asRow();
$cursor->asAssoc();
$cursor->asObject();
$cursor->asAll();
```
либо при использовании конструкции `foreach(){}`. В последнем случае неявно будет вызываться метод `asAssoc()`. Примеры условий:

```
\gear\Core::c('db')->selectCollection('database', 'products')->find(array('id' => 1, '$or' => array('id' => 4)))
соответствует SQL-запросу
SELECT products.* FROM products WHERE products.id = 1 OR products.id = 4
```
```
\gear\Core::c('db')->selectCollection('database', 'products')->find(array('id' => array('$in' => array(1 ,4)), '$and' => array('category' => 3)))
соответствует SQL-запросу
SELECT products.* FROM products WHERE products.id IN (1, 4) AND products.category = 3
```
```
\gear\Core::c('db')->selectCollection('database', 'products')->find((array('id' => 1, array('category' => 1, '$or' => array('category' => 3))))
соответствует SQL-запросу
SELECT products.* FROM products WHERE products.id = 1 AND (products.category = 1 OR products.category = 2)
```

#### Сортировка

```
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 3))
        ->sort(array('name' => 1));
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```
В данном случае будет произведена сортировка по полю `name` в порядке возрастания, т.е. `ASC`. Для сортировки в порядке убывания `sort(array('name' => -1))`. Сортировка по нескольким полям:

```
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find()
        ->sort(array('category' => 1, 'name' => 1));
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```
Возможно также указать собственный порядок сортировки, например, когда необходимо в начале вывести элементы категорий `5, 1, 3`, а потом всех остальных:
```
\gear\Core::c('db')->selectCollection('database', 'products')
                   ->find()
                   ->sort(array('category' => array(5, 1, 3), 'name' => 1));
```
#### Выборка определённого количества элементов

Для ограничения количества получаемых элементов используется метод `limit()`, например:

```
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 1))
        ->limit(3);
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```
В данном случае будет произведена выборка первых трёх элементов. 
Для выборки элементов начиная с определённого, можно использовать один из вариантов, например для выборки 3-х элементов, начиная с 5-ого:

```
limit(3, 5);
limit(array(3, 5));
limit('3, 5')
```
Запись `findOne(array('category' => 1))` равносильна `find(array('category' => 1))->limit(1)->asAssoc()`
 
#### Добавление новых записей

Для данной оперции используется метод `insert()`

> Данный метод не является отложенным, а выполняется сразу и возвращает количество добавленных записей таблицы. 

Производит простое добавление новой записи в таблицу

```
\gear\Core::c('db')->selectCollection('database', 'products')->insert(array
(
    'category' => 1,
    'name' => 'Сахар',
));
```
Если необходимо вставить несколько записей, то лучше делать это, не используя цикл, следующим образом:

```
\gear\Core::c('db')->selectCollection('database', 'products')->insert(array
(
    array
    (
        'category' => 1,
        'name' => 'Сахар',
    ),
    array
    (
        'category' => 1,
        'name' => 'Масло',
    ),
));
```
Возможна такая запись добавления объекта

```
\gear\Core::c('db')->selectCollection('database', 'products')->insert(new \gear\library\GModel(array
(
    'category' => 1,
    'name' => 'Сахар',
)));
```
> При совпадении PRIMARY KEY будет генерироваться исключение

Если в таблице присутствует поле с `AUTOINCREMENT`, то после добавления новой записи получить значение такого поля возможно с помощью метода `lastInsertId()`.

#### Обновление

#### Удаление

Для удаления элементов из таблицы используется метод `remove()`. 

> Данный метод не является отложенным, а выполняется сразу и возвращает количество затронутых записей таблицы. 

Например, чтобы удалить все элементы из категории `3`:

```
\gear\Core::c('db')->selectCollection('database', 'products')->remove(array('category' => 3));
```
Данная запись будет равносильна

```
\gear\Core::c('db')->selectCollection('database', 'products')
                   ->find(array('category' => 3))
                   ->remove();
```
Это полезно для случаев, когда необходимо, например, сначала вывести на экран выбранные элементы, а потом их удалить

```
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 3))
foreach($cursor as $itemProduct)
{
    echo 'Deleted ', $itemProduct->name, '<br />';
}
$cursor->remove();
```
Удалить все записи из таблицы можно тремя способами:

```
// SQL-Запрос DELETE FROM `products`
\gear\Core::c('db')->selectCollection('database', 'products')->find()->remove();
\gear\Core::c('db')->selectCollection('database', 'products')->remove();
// SQL-Запрос TRUNCATE TABLE `products`
\gear\Core::c('db')->selectCollection('database', 'products')->truncate();
```
