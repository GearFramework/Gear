-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 14 2017 г., 16:22
-- Версия сервера: 5.7.14-8
-- Версия PHP: 7.0.13-1+deb.sury.org~trusty+1

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
-- Структура таблицы `clientOrderProducts`
--

DROP TABLE IF EXISTS `clientOrderProducts`;
CREATE TABLE `clientOrderProducts` (
  `order` bigint(20) UNSIGNED NOT NULL,
  `product` bigint(20) UNSIGNED NOT NULL,
  `count` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clientOrders`
--

DROP TABLE IF EXISTS `clientOrders`;
CREATE TABLE `clientOrders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client` bigint(20) UNSIGNED NOT NULL,
  `dateOrder` datetime NOT NULL,
  `datePrepare` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `zipcode` varchar(12) NOT NULL,
  `country` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `addr` varchar(1024) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `lastlogin` datetime NOT NULL,
  `discount` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `articul` varchar(8) NOT NULL DEFAULT '00000000',
  `name` varchar(1024) NOT NULL,
  `description` varchar(4096) NOT NULL,
  `vendor` smallint(5) UNSIGNED NOT NULL,
  `price` double UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `hash` varchar(255) NOT NULL,
  `user` bigint(20) UNSIGNED NOT NULL,
  `sessiontime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `storage`
--

DROP TABLE IF EXISTS `storage`;
CREATE TABLE `storage` (
  `product` bigint(20) UNSIGNED NOT NULL,
  `vendor` smallint(5) UNSIGNED NOT NULL,
  `count` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `reserved` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorOrders`
--

DROP TABLE IF EXISTS `vendorOrders`;
CREATE TABLE `vendorOrders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor` smallint(5) UNSIGNED NOT NULL,
  `product` bigint(20) UNSIGNED NOT NULL,
  `lots` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendorProducts`
--

DROP TABLE IF EXISTS `vendorProducts`;
CREATE TABLE `vendorProducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(1024) NOT NULL,
  `vendor` smallint(5) UNSIGNED NOT NULL,
  `product` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vendors`
--

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(1024) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `description` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articul_index` (`articul`),
  ADD KEY `name_index` (`name`(255)),
  ADD KEY `descr_index` (`description`(255)),
  ADD KEY `vendor_index` (`vendor`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`hash`);

--
-- Индексы таблицы `vendorOrders`
--
ALTER TABLE `vendorOrders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vendorProducts`
--
ALTER TABLE `vendorProducts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
