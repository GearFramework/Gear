Gear Framework
====

PHP Framework (PHP 5.3 or higher)

====

#### Установка

Так как фреймворк задумывался также под возможность однорвеменного использования несколькими проектами, то выбирать папку для установки следует скорее всего в /usr/share/gear/gear. В данную папку распаковываем исходники фреймворка.
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

#### Пространство имён

#### Создание проекта

#### Модули

#### Компоненты

#### Плагины

#### Поведения

#### ORM

* Выборка

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

Любые `SELECT` запросы являются отложенными до вызова одного из метода полученного курсора:

```
$cursor->asRow();
$cursor->asAssoc();
$cursor->asObject();
$cursor->asAll();
```
либо при использовании конструкции `foreach(){}`. В последнем случае неявно будет вызываться метод `asAssoc()`.

* Сортировка

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
\gear\Core::c('db')->selectCollection('database', 'products')->find()->sort(array('category' => array(5, 1, 3), 'name' => 1));
```
 