-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 26 2017 г., 23:24
-- Версия сервера: 5.6.25-73.1
-- Версия PHP: 7.1.0RC6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `eb`
--
CREATE DATABASE IF NOT EXISTS `eb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `eb`;

-- --------------------------------------------------------

--
-- Структура таблицы `basket`
--

DROP TABLE IF EXISTS `basket`;
CREATE TABLE IF NOT EXISTS `basket` (
  `client` bigint(20) unsigned NOT NULL,
  `session` varchar(255) NOT NULL,
  `product` smallint(5) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientLocations`
--

DROP TABLE IF EXISTS `clientLocations`;
CREATE TABLE IF NOT EXISTS `clientLocations` (
  `id` bigint(20) unsigned NOT NULL,
  `client` bigint(20) unsigned NOT NULL,
  `zipCode` varchar(12) NOT NULL,
  `country` varchar(128) NOT NULL,
  `state` varchar(512) NOT NULL,
  `city` varchar(512) NOT NULL,
  `address` varchar(512) NOT NULL,
  `prefer` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientOrderProducts`
--

DROP TABLE IF EXISTS `clientOrderProducts`;
CREATE TABLE IF NOT EXISTS `clientOrderProducts` (
  `order` bigint(20) unsigned NOT NULL,
  `product` bigint(20) unsigned NOT NULL,
  `count` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientOrders`
--

DROP TABLE IF EXISTS `clientOrders`;
CREATE TABLE IF NOT EXISTS `clientOrders` (
  `id` bigint(20) unsigned NOT NULL,
  `client` bigint(20) unsigned NOT NULL,
  `locationDelivery` bigint(20) unsigned NOT NULL,
  `notice` varchar(512) NOT NULL,
  `typePayment` tinyint(1) unsigned NOT NULL,
  `typeDelivery` tinyint(1) unsigned NOT NULL,
  `priceDelivery` double unsigned NOT NULL,
  `dateCheckout` datetime NOT NULL,
  `datePrepared` datetime NOT NULL,
  `datePayment` datetime NOT NULL,
  `datePutDelivery` datetime NOT NULL,
  `dateClientReceived` datetime NOT NULL,
  `status` tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientOrderStatuses`
--

DROP TABLE IF EXISTS `clientOrderStatuses`;
CREATE TABLE IF NOT EXISTS `clientOrderStatuses` (
  `idClientOrderStatus` tinyint(3) unsigned NOT NULL,
  `nameClientOrderStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `clientOrderStatuses`
--

INSERT INTO `clientOrderStatuses` (`idClientOrderStatus`, `nameClientOrderStatus`) VALUES
(1, 'Оформлен'),
(2, 'Ожидает оплаты'),
(3, 'Оплачен'),
(4, 'Обработан'),
(5, 'Отправлен клиенту'),
(6, 'Получен клиентом'),
(7, 'Возврат'),
(8, 'Возврат доставкой'),
(9, 'Возврат на склад'),
(10, 'Возврат оплаты'),
(11, 'Завершен');

-- --------------------------------------------------------

--
-- Структура таблицы `clientOrderWorkflow`
--

DROP TABLE IF EXISTS `clientOrderWorkflow`;
CREATE TABLE IF NOT EXISTS `clientOrderWorkflow` (
  `id` bigint(20) unsigned NOT NULL,
  `clientOrder` bigint(20) unsigned NOT NULL,
  `statusOrder` tinyint(3) unsigned NOT NULL,
  `operator` tinyint(3) unsigned NOT NULL,
  `dateAction` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint(20) unsigned NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `zipcode` varchar(12) NOT NULL,
  `dateRegistered` datetime NOT NULL,
  `dateFirstOrder` datetime NOT NULL,
  `dateLastOrder` datetime NOT NULL,
  `lastLogin` datetime NOT NULL,
  `countOrders` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `confirmRegisterKey` varchar(128) NOT NULL,
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientSessions`
--

DROP TABLE IF EXISTS `clientSessions`;
CREATE TABLE IF NOT EXISTS `clientSessions` (
  `hash` varchar(255) NOT NULL,
  `token` varchar(128) NOT NULL,
  `client` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timeSession` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientStatuses`
--

DROP TABLE IF EXISTS `clientStatuses`;
CREATE TABLE IF NOT EXISTS `clientStatuses` (
  `idClientStatus` tinyint(1) NOT NULL,
  `nameClientStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `clientStatuses`
--

INSERT INTO `clientStatuses` (`idClientStatus`, `nameClientStatus`) VALUES
(1, 'Постоянный'),
(2, 'Требует внимания'),
(3, 'Заблокирован'),
(4, 'Требуется регистрация'),
(5, 'Ожидается подтверждение регистрации');

-- --------------------------------------------------------

--
-- Структура таблицы `costs`
--

DROP TABLE IF EXISTS `costs`;
CREATE TABLE IF NOT EXISTS `costs` (
  `amount` double unsigned NOT NULL,
  `dateAmount` datetime NOT NULL,
  `vendorOrder` bigint(20) unsigned NOT NULL DEFAULT '0',
  `description` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `incomes`
--

DROP TABLE IF EXISTS `incomes`;
CREATE TABLE IF NOT EXISTS `incomes` (
  `amount` double unsigned NOT NULL,
  `dateAmount` datetime NOT NULL,
  `clientOrder` bigint(20) unsigned NOT NULL DEFAULT '0',
  `description` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `operators`
--

DROP TABLE IF EXISTS `operators`;
CREATE TABLE IF NOT EXISTS `operators` (
  `id` bigint(20) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint(20) unsigned NOT NULL,
  `client` bigint(20) unsigned NOT NULL,
  `clientOrder` bigint(20) unsigned NOT NULL,
  `pricePending` double unsigned NOT NULL,
  `description` varchar(512) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `datePrepared` datetime NOT NULL,
  `datePayment` datetime DEFAULT NULL,
  `paymentIncome` double unsigned NOT NULL DEFAULT '0',
  `paymentDescription` varchar(1024) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `paymentStatuses`
--

DROP TABLE IF EXISTS `paymentStatuses`;
CREATE TABLE IF NOT EXISTS `paymentStatuses` (
  `idPaymentStatus` tinyint(1) unsigned NOT NULL,
  `namePaymentStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `paymentStatuses`
--

INSERT INTO `paymentStatuses` (`idPaymentStatus`, `namePaymentStatus`) VALUES
(1, 'Ожидание оплаты'),
(2, 'Оплачено');

-- --------------------------------------------------------

--
-- Структура таблицы `productComposition`
--

DROP TABLE IF EXISTS `productComposition`;
CREATE TABLE IF NOT EXISTS `productComposition` (
  `product` smallint(5) unsigned NOT NULL,
  `productVendor` smallint(5) unsigned NOT NULL,
  `vendorOrder` bigint(20) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `productProperties`
--

DROP TABLE IF EXISTS `productProperties`;
CREATE TABLE IF NOT EXISTS `productProperties` (
  `property` tinyint(3) unsigned NOT NULL,
  `product` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `productPropertyValues`
--

DROP TABLE IF EXISTS `productPropertyValues`;
CREATE TABLE IF NOT EXISTS `productPropertyValues` (
  `product` smallint(5) unsigned NOT NULL,
  `propertyName` varchar(128) NOT NULL,
  `propertyValue` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `productReserveComposition`
--

DROP TABLE IF EXISTS `productReserveComposition`;
CREATE TABLE IF NOT EXISTS `productReserveComposition` (
  `reserve` bigint(20) unsigned NOT NULL,
  `productVendor` smallint(5) unsigned NOT NULL,
  `vendorOrder` bigint(20) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `productReserves`
--

DROP TABLE IF EXISTS `productReserves`;
CREATE TABLE IF NOT EXISTS `productReserves` (
  `id` int(11) NOT NULL,
  `product` smallint(5) unsigned NOT NULL,
  `client` bigint(20) unsigned NOT NULL DEFAULT '0',
  `clientOrder` bigint(20) unsigned NOT NULL DEFAULT '0',
  `dateReserved` datetime NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `count` smallint(5) unsigned NOT NULL,
  `description` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `productReserveTypes`
--

DROP TABLE IF EXISTS `productReserveTypes`;
CREATE TABLE IF NOT EXISTS `productReserveTypes` (
  `idProductServerType` tinyint(1) unsigned NOT NULL,
  `nameProductServerType` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `productReserveTypes`
--

INSERT INTO `productReserveTypes` (`idProductServerType`, `nameProductServerType`) VALUES
(1, 'Оформленный заказ в магазине'),
(2, 'Резерв на сайте постоянным клиентом'),
(3, 'Резерв по телефону');

-- --------------------------------------------------------

--
-- Структура таблицы `productResources`
--

DROP TABLE IF EXISTS `productResources`;
CREATE TABLE IF NOT EXISTS `productResources` (
  `id` mediumint(8) unsigned NOT NULL,
  `product` smallint(5) unsigned NOT NULL,
  `mime` varchar(128) NOT NULL,
  `path` varchar(1024) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` smallint(5) unsigned NOT NULL,
  `category` smallint(5) unsigned NOT NULL,
  `articul` varchar(8) NOT NULL DEFAULT '00000000',
  `name` varchar(1024) NOT NULL,
  `price` double unsigned NOT NULL DEFAULT '0',
  `discount` double unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` tinyint(3) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `hash` varchar(255) NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  `sessiontime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `storage`
--

DROP TABLE IF EXISTS `storage`;
CREATE TABLE IF NOT EXISTS `storage` (
  `product` smallint(5) unsigned NOT NULL,
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `reserved` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorCategories`
--

DROP TABLE IF EXISTS `vendorCategories`;
CREATE TABLE IF NOT EXISTS `vendorCategories` (
  `vendor` smallint(5) unsigned NOT NULL,
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent` smallint(5) unsigned NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `key` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorOrderLots`
--

DROP TABLE IF EXISTS `vendorOrderLots`;
CREATE TABLE IF NOT EXISTS `vendorOrderLots` (
  `id` bigint(20) unsigned NOT NULL,
  `vendorOrder` bigint(20) unsigned NOT NULL,
  `productVendor` smallint(5) unsigned NOT NULL,
  `countLots` smallint(5) unsigned NOT NULL,
  `countProductsPerLot` smallint(5) unsigned NOT NULL DEFAULT '1',
  `priceLot` double unsigned NOT NULL,
  `priceDelivery` double unsigned NOT NULL DEFAULT '0',
  `statusLot` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Лоты в заказах у поставщиков';

-- --------------------------------------------------------

--
-- Структура таблицы `vendorOrderLotStatuses`
--

DROP TABLE IF EXISTS `vendorOrderLotStatuses`;
CREATE TABLE IF NOT EXISTS `vendorOrderLotStatuses` (
  `idLotStatus` tinyint(1) unsigned NOT NULL,
  `nameLotStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vendorOrderLotStatuses`
--

INSERT INTO `vendorOrderLotStatuses` (`idLotStatus`, `nameLotStatus`) VALUES
(1, 'Оплачено'),
(2, 'Отправлено'),
(3, 'Получено'),
(4, 'На складе'),
(5, 'Утеряно'),
(6, 'Возврат'),
(7, 'Открыт спор');

-- --------------------------------------------------------

--
-- Структура таблицы `vendorOrders`
--

DROP TABLE IF EXISTS `vendorOrders`;
CREATE TABLE IF NOT EXISTS `vendorOrders` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `vendor` smallint(5) unsigned NOT NULL,
  `statusOrder` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `preparingDays` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `dateOrder` datetime NOT NULL,
  `datePayment` datetime NOT NULL,
  `dateSent` datetime NOT NULL,
  `dateReceived` datetime NOT NULL,
  `dateStoraged` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorOrderStatuses`
--

DROP TABLE IF EXISTS `vendorOrderStatuses`;
CREATE TABLE IF NOT EXISTS `vendorOrderStatuses` (
  `idVendorOrderStatus` tinyint(1) unsigned NOT NULL,
  `nameVendorOrderStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vendorOrderStatuses`
--

INSERT INTO `vendorOrderStatuses` (`idVendorOrderStatus`, `nameVendorOrderStatus`) VALUES
(1, 'Оформлен'),
(2, 'Оплачен'),
(3, 'Отправлено'),
(4, 'Получено'),
(5, 'На складе'),
(6, 'Утеряно'),
(7, 'Возврат'),
(8, 'Открыт спор'),
(9, 'Возврат оплаты'),
(10, 'Завершено');

-- --------------------------------------------------------

--
-- Структура таблицы `vendorOrderWorkflow`
--

DROP TABLE IF EXISTS `vendorOrderWorkflow`;
CREATE TABLE IF NOT EXISTS `vendorOrderWorkflow` (
  `id` bigint(20) unsigned NOT NULL,
  `vendorOrder` bigint(20) unsigned NOT NULL,
  `statusOrder` tinyint(3) unsigned NOT NULL,
  `operator` tinyint(3) unsigned NOT NULL,
  `dateAction` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorProducts`
--

DROP TABLE IF EXISTS `vendorProducts`;
CREATE TABLE IF NOT EXISTS `vendorProducts` (
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(1024) NOT NULL,
  `vendor` smallint(5) unsigned NOT NULL,
  `categoryVendor` smallint(5) unsigned NOT NULL,
  `productStorage` smallint(5) unsigned NOT NULL,
  `url` varchar(1024) NOT NULL,
  `statusVendorProduct` tinyint(1) unsigned NOT NULL,
  `description` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorProductStatuses`
--

DROP TABLE IF EXISTS `vendorProductStatuses`;
CREATE TABLE IF NOT EXISTS `vendorProductStatuses` (
  `idVendorProductStatus` tinyint(1) unsigned NOT NULL,
  `nameVendorProductStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vendorProductStatuses`
--

INSERT INTO `vendorProductStatuses` (`idVendorProductStatus`, `nameVendorProductStatus`) VALUES
(1, 'Доступен к заказу'),
(2, 'Недоступен к заказу'),
(3, 'Запрещен к заказу');

-- --------------------------------------------------------

--
-- Структура таблицы `vendors`
--

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` smallint(5) unsigned NOT NULL,
  `name` varchar(1024) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `statusVendor` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorStatuses`
--

DROP TABLE IF EXISTS `vendorStatuses`;
CREATE TABLE IF NOT EXISTS `vendorStatuses` (
  `idVendorStatus` tinyint(1) unsigned NOT NULL,
  `nameVendorStatus` varchar(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vendorStatuses`
--

INSERT INTO `vendorStatuses` (`idVendorStatus`, `nameVendorStatus`) VALUES
(1, 'Создан'),
(2, 'Оплачен'),
(3, 'Отправлен'),
(4, 'Получен'),
(5, 'Поступил на склад'),
(6, 'Возврат поставщику'),
(7, 'Утерян при пересылке'),
(8, 'Не отправлен');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `basket`
--
ALTER TABLE `basket`
  ADD KEY `client` (`client`),
  ADD KEY `session` (`session`);

--
-- Индексы таблицы `clientLocations`
--
ALTER TABLE `clientLocations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `clientOrderStatuses`
--
ALTER TABLE `clientOrderStatuses`
  ADD PRIMARY KEY (`idClientOrderStatus`);

--
-- Индексы таблицы `clientOrderWorkflow`
--
ALTER TABLE `clientOrderWorkflow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clientOrder` (`clientOrder`);

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `clientSessions`
--
ALTER TABLE `clientSessions`
  ADD PRIMARY KEY (`hash`),
  ADD KEY `client` (`client`);

--
-- Индексы таблицы `clientStatuses`
--
ALTER TABLE `clientStatuses`
  ADD PRIMARY KEY (`idClientStatus`);

--
-- Индексы таблицы `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `paymentStatuses`
--
ALTER TABLE `paymentStatuses`
  ADD PRIMARY KEY (`idPaymentStatus`);

--
-- Индексы таблицы `productComposition`
--
ALTER TABLE `productComposition`
  ADD UNIQUE KEY `vendorOrder_indexUnique` (`vendorOrder`);

--
-- Индексы таблицы `productPropertyValues`
--
ALTER TABLE `productPropertyValues`
  ADD KEY `productPropertyValues_index` (`product`);

--
-- Индексы таблицы `productReserves`
--
ALTER TABLE `productReserves`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `productReserveTypes`
--
ALTER TABLE `productReserveTypes`
  ADD PRIMARY KEY (`idProductServerType`);

--
-- Индексы таблицы `productResources`
--
ALTER TABLE `productResources`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articul_index` (`articul`),
  ADD KEY `name_index` (`name`(255));

--
-- Индексы таблицы `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`hash`);

--
-- Индексы таблицы `vendorCategories`
--
ALTER TABLE `vendorCategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_index` (`vendor`);

--
-- Индексы таблицы `vendorOrderLots`
--
ALTER TABLE `vendorOrderLots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productVendor_index` (`productVendor`);

--
-- Индексы таблицы `vendorOrderLotStatuses`
--
ALTER TABLE `vendorOrderLotStatuses`
  ADD PRIMARY KEY (`idLotStatus`);

--
-- Индексы таблицы `vendorOrders`
--
ALTER TABLE `vendorOrders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vendorOrderStatuses`
--
ALTER TABLE `vendorOrderStatuses`
  ADD PRIMARY KEY (`idVendorOrderStatus`);

--
-- Индексы таблицы `vendorOrderWorkflow`
--
ALTER TABLE `vendorOrderWorkflow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clientOrder` (`vendorOrder`);

--
-- Индексы таблицы `vendorProducts`
--
ALTER TABLE `vendorProducts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vendorProductStatuses`
--
ALTER TABLE `vendorProductStatuses`
  ADD PRIMARY KEY (`idVendorProductStatus`);

--
-- Индексы таблицы `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vendorStatuses`
--
ALTER TABLE `vendorStatuses`
  ADD PRIMARY KEY (`idVendorStatus`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clientLocations`
--
ALTER TABLE `clientLocations`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `clientOrderStatuses`
--
ALTER TABLE `clientOrderStatuses`
  MODIFY `idClientOrderStatus` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT для таблицы `clientOrderWorkflow`
--
ALTER TABLE `clientOrderWorkflow`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `clientStatuses`
--
ALTER TABLE `clientStatuses`
  MODIFY `idClientStatus` tinyint(1) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `operators`
--
ALTER TABLE `operators`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `paymentStatuses`
--
ALTER TABLE `paymentStatuses`
  MODIFY `idPaymentStatus` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `productReserves`
--
ALTER TABLE `productReserves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `productReserveTypes`
--
ALTER TABLE `productReserveTypes`
  MODIFY `idProductServerType` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `productResources`
--
ALTER TABLE `productResources`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `properties`
--
ALTER TABLE `properties`
  MODIFY `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorCategories`
--
ALTER TABLE `vendorCategories`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorOrderLots`
--
ALTER TABLE `vendorOrderLots`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorOrderLotStatuses`
--
ALTER TABLE `vendorOrderLotStatuses`
  MODIFY `idLotStatus` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `vendorOrders`
--
ALTER TABLE `vendorOrders`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorOrderStatuses`
--
ALTER TABLE `vendorOrderStatuses`
  MODIFY `idVendorOrderStatus` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `vendorOrderWorkflow`
--
ALTER TABLE `vendorOrderWorkflow`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorProducts`
--
ALTER TABLE `vendorProducts`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorProductStatuses`
--
ALTER TABLE `vendorProductStatuses`
  MODIFY `idVendorProductStatus` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendorStatuses`
--
ALTER TABLE `vendorStatuses`
  MODIFY `idVendorStatus` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
