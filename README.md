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

Процессы
--------

В понимании MVC процессы являются контроллерами(controllers). За вызов того или иного процесса отвечает компонент - "Менеджер процессов", описанный в конфигурации класса `gear\library\GApplication` и переопределить который можно в конфигурационном файле приложения, например:

```php
\gear\Core::init
(
    array
    (
        'modules` => array
        (
            'app' => array
            (
                'components' => array
                (
                    'process' => array
                    (
                        'class' => '\\myproject\\components\\GMyProcessManager',
                    ),
                ),
            ),
        ),
    )
);
```

По-умолчанию же используется стандартный менеджер процессов `\gear\components\gear\process\GProcessComponent`. Функция менеджера процессов - получить из запроса название процесса, определить его расположение, создать экземпляр класса процесса и запустить его. В запросе название процесса определяется параметром <b>e</b>:
```
http://localhost?e=processName
```
В зависимости от типа запроса POST или GET, параметр <b>e</b> будет искаться либо в `$_POST` либо в `$_GET`-массиве соответственно. При отсутствии параметра, название процесса будет браться из статического поля `defaultProcess`. Все файлы процессов должны располагаться в папке `process` либо внутри проекта, либо какого-либо модуля.
Определение класса, т.е. расположения процесса лучше всего показать на примерах:

Для `http://localhost?e=processName` класс `\\currentProject\\process\\GProcessName`

Для `http://localhost?e=/gear/processName` класс `\\gear\\process\\GProcessName`

Для `http://localhost?e=/projectName/processName` класс `\\projectName\\process\\GProcessName`

Для `http://localhost?e=moduleName/processName` класс `\\currentProject\\moduleName\\process\\GProcessName`

Для `http://localhost?e=/gear/moduleName/processName` класс `\\gear\\moduleName\\process\\GProcessName`

Для `http://localhost?e=/projectName/moduleName/processName` класс `\\projectName\\moduleName\\process\\GProcessName`

API-методы
----------

В понимании MVC api-методы являются экшенами(actions).

Создание проекта
----------------

Все запросы должны проходить через единственный файл index.php, который в общем случае, располагается в `DOCUMENT_ROOT` вашего Apache-сервера (допустим это будет `/var/www`). Помимо index.php рядом могут располагаться конфигурационные файлы `config.production.php` и/или `config.debug.php`, в зависимости от режима работы приложения (config.production.php - настройки приложения для работы в продакшене, config.debug.php - настройки приолжения для работы в режиме разработки и отладки), но также возможно использовать любой иной файл-конфигурации (при инициализации ядра необходимо указать путь к данному файлу).
В минимальном виде index.php содержит три строчки кода:

```php
<?php
require '/usr/share/gear/gear/Core.php';
\gear\Core::init();
\gear\Core::app()->run();
```
Первой строкой подключается ядро фреймворка. Далее следует инициализация ядра посредством вызова метода `init()`. Процесс инициализации представляет собой чтение конфигурационного файла (если таковой имеется), и исходя из его настроек - подключение необходимых библиотек, модулей, компонентов.
Метод `\gear\Core::init()` имеет два необязательных параметра. Первый, собственно, определяет конфигурацию, второй устанавливает режим работы приложения: разработка/отладка(определяется константой `\gear\Core::MODE_DEVELOPMENT`), рабочая версия (определяется константой `\gear\Core::MODE_PRODUCTION`). По-умоланию, второй параметр имеет значение `\gear\Core::MODE_DEVELOPMENT`.
В зависимости от значения первого параметра процесс инициализации может происходить несколькими путями:

1. Первый параметр не указан. В этом случае, метод `\gear\Core::init()` попытается найти конфигурационный файл в папке, где располагается непосредственно `index.php`, в зависимости от режима работы приложения это может быть либо config.debug.php, либо config.production.php, если таковые не найдены, то генерируется исключение `\gear\CoreException`.

2. Непосредственно массив с настройками
```php
\gear\Core::init(array
(
    'modules' => array
    (
        'app' => array('class' => '\\myproject\\MyProject'),
    ),
));
```
```php
\gear\Core::init(require('/var/www/config.myproject.php'));
```

3. Путь к папке в которой должны располагаться config.debug.php или/и config.production.php
```php
\gear\Core::init('/usr/share/gear/myproject/config');
```

4. Непосредственно путь и название файла конфигурации. В данном случае возможно использование пространства имён.
```php
\gear\Core::init('/usr/share/gear/myproject/config/config.test.php');
// пространство имён относительно /usr/share/gear
\gear\Core::init('\\myproject\\config\\config.test.php');
// пространство имён относительно /usr/share/gear/gear, т.е. файл config.test.php 
// должен лежать по физическому пути /usr/share/gear/gear/config по той простой причине,
// что на момент подключения конфигурационного файла объекта приложения ещё не существует
\gear\Core::init('config\\config.test.php');
```

Полученные таким образом настройки объединяются с теми, которые по-умолчанию уже определены в ядре.

#### Конфигурирование

В конфигурационном файле, в общем случае, существуют 3 раздела:

1. preloads
2. modules
3. components

<b>preloads</b> служит для подключения критически важных библиотек классов, модулей и компонентов, экземляры которых будут созданы на этапе конфигурирования, например, по-умолчанию здесь определены: базовые классы, а также автозагрузчик, обработчики исключений и ошибок. Данный раздел содержит 3 подраздела: `library`, `modules`, `components`.
<b>modules</b> служит для определения модулей.

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

Дл яполучения доступа к базам данных используются соответствующие компоненты. Для этого, необходимо в конфигурационном файле, в соответствующем разделе `components` включить его описание

```php
...
'components' => array
(
    'db' => array
    (
        'class' => '\\gear\\components\\gear\\db\\mysql\\GMySql'
        'host' => 'localhost',
        'username' => 'admin',
        'password' => '',
    ),
),
...
```

Описать таким образом можно необходимое количество компонентов, если у вас не один, а несколько северов баз данных. После этого в приложении можно будет обратиться к данном компоненту как `\gear\Core::c('db');`. Компонент поддерживает работу с несколькими база данных на одном сервере:

```php
$database = \gear\Core::c('db')->selectDB('databaseName');
```

Для получения конкретных данных, помимо выбора базы данных, необходимо указать рабочую таблицу

```php
$table = \gear\Core::c('db')->selectDB('databaseName')->selectCollection('tableName');
```

Оба эти действия можно объеденить в одно

```php
$table = \gear\Core::c('db')->selectCollection('databaseName', 'tableName');
```

#### Выборка

Самая простая выборка, соответствующая SQL-запросу `SELECT * FROM products`:

```php
$collection = \gear\Core::c('db')->selectCollection('database', 'products');
foreach($collection as $itemProduct)
    echo $itemProduct->name, '<br />';
```
Условие выборки:
 
```php
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 3));
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```

> Любые `SELECT` запросы являются отложенными до вызова одного из методов полученного курсора:

```php
$cursor->asRow();
$cursor->asAssoc();
$cursor->asObject();
$cursor->asAll();
```
либо при использовании конструкции `foreach(){}`. В последнем случае неявно будет вызываться метод `asAssoc()`. Примеры условий:

```php
\gear\Core::c('db')->selectCollection('database', 'products')->find
(
    array
    (
        'id' => 1, 
        '$or' => array('id' => 4)
    )
);
```
соответствует SQL-запросу
```sql
SELECT products.* FROM products WHERE products.id = 1 OR products.id = 4
```
---
```php
\gear\Core::c('db')->selectCollection('database', 'products')->find
(
    array
    (
        'id' => array('$in' => array(1 ,4)), 
        '$and' => array('category' => 3)
    )
);
```
соответствует SQL-запросу
```sql
SELECT products.* FROM products WHERE products.id IN (1, 4) AND products.category = 3
```
---
```php
\gear\Core::c('db')->selectCollection('database', 'products')->find
(
    array
    (
        'id' => 1, 
        array('category' => 1, '$or' => array('category' => 3))
    )
);
```
соответствует SQL-запросу
```sql
SELECT products.* FROM products WHERE products.id = 1 AND (products.category = 1 OR products.category = 2)
```

#### Сортировка

```php
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 3))
        ->sort(array('name' => 1));
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```
В данном случае будет произведена сортировка по полю `name` в порядке возрастания, т.е. `ASC`. Для сортировки в порядке убывания `sort(array('name' => -1))`. Сортировка по нескольким полям:

```php
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find()
        ->sort(array('category' => 1, 'name' => 1));
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```
Возможно также указать собственный порядок сортировки, например, когда необходимо в начале вывести элементы категорий `5, 1, 3`, а потом всех остальных:
```php
\gear\Core::c('db')->selectCollection('database', 'products')
                   ->find()
                   ->sort(array('category' => array(5, 1, 3), 'name' => 1));
```
#### Выборка определённого количества элементов

Для ограничения количества получаемых элементов используется метод `limit()`, например:

```php
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 1))
        ->limit(3);
foreach($cursor as $itemProduct)
    echo $itemProduct->name, '<br />';
```
В данном случае будет произведена выборка первых трёх элементов. 
Для выборки элементов начиная с определённого, можно использовать один из вариантов, например для выборки 3-х элементов, начиная с 5-ого:

```php
limit(3, 5);
limit(array(3, 5));
limit('3, 5')
```
Запись `findOne(array('category' => 1))` равносильна `find(array('category' => 1))->limit(1)->asAssoc()`.

> Порядок использования методов: `find()`, `sort()`, `limit()` и прочих не имеет значения, т.е. запись
>
> ```php 
 \gear\Core::c('db')->selectCollection('database', 'products')
  ->find(array('category' => 1))
  ->sort(array('name' => -1))
  ->limit(3); 
  ```
>
> равносильна записи
>
> ```php 
 \gear\Core::c('db')->selectCollection('database', 'products')
 ->limit(3)
 ->sort(array('name' => -1))
 ->find(array('category' => 1));
 ```
 
#### Добавление новых записей

Для данной оперции используется метод `insert()`

> Данный метод не является отложенным, а выполняется сразу и возвращает количество добавленных записей таблицы. 

Производит простое добавление новой записи в таблицу

```php
\gear\Core::c('db')->selectCollection('database', 'products')->insert(array
(
    'category' => 1,
    'name' => 'Сахар',
));
```
Если необходимо вставить несколько записей, то лучше делать это, не используя цикл, следующим образом:

```php
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

```php
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

```php
\gear\Core::c('db')->selectCollection('database', 'products')->remove(array('category' => 3));
```
Данная запись будет равносильна

```php
\gear\Core::c('db')->selectCollection('database', 'products')
                   ->find(array('category' => 3))
                   ->remove();
```
Это полезно для случаев, когда необходимо, например, сначала вывести на экран выбранные элементы, а потом их удалить

```php
$cursor = \gear\Core::c('db')->selectCollection('database', 'products')
        ->find(array('category' => 3))
foreach($cursor as $itemProduct)
{
    echo 'Deleted ', $itemProduct->name, '<br />';
}
$cursor->remove();
```
Удалить все записи из таблицы можно тремя способами:

```php
// SQL-Запрос DELETE FROM `products`
\gear\Core::c('db')->selectCollection('database', 'products')->find()->remove();
\gear\Core::c('db')->selectCollection('database', 'products')->remove();
// SQL-Запрос TRUNCATE TABLE `products`
\gear\Core::c('db')->selectCollection('database', 'products')->truncate();
```
