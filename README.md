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

Все запросы должны проходить через единственный файл index.php, который в общем случае, располагается в `DOCUMENT_ROOT` вашего Apache-сервера (допустим это будет `/var/www`). Помимо index.php рядом могут располагаться конфигурационные файлы `config.production.php` и/или `config.debug.php`, в зависимости от режима работы приложения.
В минимальном виде index.php содержит три строчки кода:

```
<?php
require '/usr/share/gear/gear/Core.php';
\gear\Core::init(__DIR__, 1);
\gear\Core::app()->run();
```
Первой строкой подключается ядро фреймворка. Следующей - производится инициализация и конфигурирование вашего приложения. Метод `init()`

Модули
------

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
либо при использовании конструкции `foreach(){}`. В последнем случае неявно будет вызываться метод `asAssoc()`.

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
