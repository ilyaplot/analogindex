-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Авг 21 2014 г., 00:26
-- Версия сервера: 5.6.16
-- Версия PHP: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- База данных: `newai`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ai_brands`
--

CREATE TABLE IF NOT EXISTS `ai_brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Название бренда',
  `link` varchar(100) NOT NULL COMMENT 'Ссылка',
  `logo` int(11) NOT NULL DEFAULT '0' COMMENT 'id из таблицы images - логотип',
  PRIMARY KEY (`id`),
  UNIQUE KEY `link` (`link`),
  KEY `logo` (`logo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Производители' AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `ai_brands`
--

INSERT INTO `ai_brands` (`id`, `name`, `link`, `logo`) VALUES
(1, 'Nokia', 'nokia', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `ai_brands_descriptions`
--

CREATE TABLE IF NOT EXISTS `ai_brands_descriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand` int(10) unsigned NOT NULL COMMENT 'id из таблицы brands',
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'код языка',
  `description` text NOT NULL COMMENT 'Описание',
  PRIMARY KEY (`id`),
  KEY `brand` (`brand`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Описания к брендам' AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `ai_brands_descriptions`
--

INSERT INTO `ai_brands_descriptions` (`id`, `brand`, `lang`, `description`) VALUES
(1, 1, 'ru', 'Тест');

-- --------------------------------------------------------

--
-- Структура таблицы `ai_brands_synonims`
--

CREATE TABLE IF NOT EXISTS `ai_brands_synonims` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Синонимы брендов для парсинга' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_characteristics`
--

CREATE TABLE IF NOT EXISTS `ai_characteristics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catalog` int(11) NOT NULL DEFAULT '0' COMMENT 'id из таблицы characteristics_catalogs',
  `formatter` varchar(200) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '100' COMMENT 'Порядок сортировки (DESC)',
  PRIMARY KEY (`id`),
  KEY `type` (`catalog`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Характеристики товаров' AUTO_INCREMENT=31 ;

--
-- Дамп данных таблицы `ai_characteristics`
--

INSERT INTO `ai_characteristics` (`id`, `catalog`, `formatter`, `priority`) VALUES
(1, 1, 'formatNone', 255),
(2, 1, 'formatNone', 254),
(3, 1, 'formatWeight', 253),
(4, 1, 'formatDimensions', 252),
(5, 2, 'formatNone', 255),
(6, 2, 'formatFreq', 254),
(7, 2, 'formatNone', 253),
(8, 3, 'formatSize', 255),
(9, 3, 'formatSize', 254),
(10, 3, 'formatSize', 253),
(11, 4, 'formatNone', 255),
(12, 4, 'formatScreenResolution', 250),
(13, 4, 'formatNone', 245),
(14, 5, 'formatNone', 255),
(15, 6, 'formatArrayComma', 255),
(16, 4, 'formatNone', 200),
(17, 6, 'formatNone', 240),
(18, 6, 'formatArrayComma', 220),
(19, 6, 'formatNone', 210),
(20, 6, 'formatNone', 200),
(21, 6, 'formatNone', 190),
(22, 7, 'formatNone', 255),
(23, 7, 'formatNone', 255),
(24, 7, 'formatBatteryTime', 240),
(25, 7, 'formatBatteryTime', 230),
(26, 8, 'formatScreenResolution', 255),
(27, 8, 'formatCameraMegapixels', 255),
(28, 8, 'formatNone', 240),
(29, 8, 'formatCameraMegapixels', 235),
(30, 10, 'formatArrayComma', 255);

-- --------------------------------------------------------

--
-- Структура таблицы `ai_characteristics_catalogs`
--

CREATE TABLE IF NOT EXISTS `ai_characteristics_catalogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '100' COMMENT 'Приоритет сортировки (desc)',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Категории характеристик' AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `ai_characteristics_catalogs`
--

INSERT INTO `ai_characteristics_catalogs` (`id`, `parent`, `priority`) VALUES
(1, 0, 255),
(2, 0, 250),
(3, 0, 245),
(4, 0, 240),
(5, 0, 235),
(6, 0, 225),
(7, 0, 220),
(8, 0, 215),
(9, 0, 210),
(10, 0, 205),
(11, 4, 255),
(12, 4, 250),
(13, 6, 245),
(14, 6, 240),
(15, 8, 235),
(16, 8, 230);

-- --------------------------------------------------------

--
-- Структура таблицы `ai_characteristics_catalogs_names`
--

CREATE TABLE IF NOT EXISTS `ai_characteristics_catalogs_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catalog` int(11) NOT NULL COMMENT 'id из таблицы characteristics_catalogs',
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `name` varchar(255) NOT NULL COMMENT 'Название категории',
  `description` text NOT NULL COMMENT 'Описание',
  PRIMARY KEY (`id`),
  KEY `catalog` (`catalog`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Названия каталогов характеристик (мультиязычность)' AUTO_INCREMENT=33 ;

--
-- Дамп данных таблицы `ai_characteristics_catalogs_names`
--

INSERT INTO `ai_characteristics_catalogs_names` (`id`, `catalog`, `lang`, `name`, `description`) VALUES
(1, 1, 'ru', 'Основные', ''),
(2, 1, 'en', 'General', ''),
(3, 2, 'ru', 'Процессор', ''),
(4, 2, 'en', 'CPU', ''),
(5, 3, 'ru', 'Память', ''),
(6, 3, 'en', 'Memory', ''),
(7, 4, 'ru', 'Экран', ''),
(8, 4, 'en', 'Display', ''),
(9, 5, 'ru', 'Операционная система', ''),
(10, 5, 'en', 'OS', ''),
(11, 6, 'ru', 'Связь', ''),
(12, 6, 'en', 'Communications', ''),
(13, 7, 'ru', 'Батарея', ''),
(14, 7, 'en', 'Battery', ''),
(15, 8, 'ru', 'Камера', ''),
(16, 7, 'en', 'Camera', ''),
(17, 9, 'ru', 'Разъемы', ''),
(18, 9, 'en', 'Connectors', ''),
(19, 10, 'ru', 'Сенсоры и датчики', ''),
(20, 10, 'en', 'Sensors and probes', ''),
(21, 11, 'ru', 'Основной', ''),
(22, 11, 'en', 'Primary', ''),
(23, 12, 'ru', 'Дополнительный', ''),
(24, 12, 'en', 'Secondary', ''),
(25, 13, 'ru', 'GSM', ''),
(26, 13, 'en', 'GSM', ''),
(27, 14, 'ru', 'Wi-Fi', ''),
(28, 14, 'en', 'Wi-FI', ''),
(29, 15, 'ru', 'Основная', ''),
(30, 15, 'en', 'Primary', ''),
(31, 16, 'ru', 'Дополнительная', ''),
(32, 16, 'en', 'Secondary', '');

-- --------------------------------------------------------

--
-- Структура таблицы `ai_characteristics_names`
--

CREATE TABLE IF NOT EXISTS `ai_characteristics_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `characteristic` int(10) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(4) NOT NULL DEFAULT 'ru',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `characteristic` (`characteristic`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Названия характеристик' AUTO_INCREMENT=61 ;

--
-- Дамп данных таблицы `ai_characteristics_names`
--

INSERT INTO `ai_characteristics_names` (`id`, `characteristic`, `lang`, `name`, `description`) VALUES
(1, 1, 'ru', 'Начало продаж', ''),
(2, 1, 'en', 'The sales start', ''),
(3, 2, 'ru', 'Доступность в магазинах', ''),
(4, 2, 'en', 'Available', ''),
(5, 3, 'ru', 'Вес', ''),
(6, 3, 'en', 'Weight', ''),
(7, 4, 'ru', 'Габариты', ''),
(8, 4, 'en', 'Dimensions', ''),
(9, 5, 'ru', 'Количество ядер процессора', ''),
(10, 5, 'en', 'Number of CPU cores', ''),
(11, 6, 'ru', 'Частота', ''),
(12, 6, 'en', 'Frequency', ''),
(13, 7, 'ru', 'Модель процессора (Чипсет)', ''),
(14, 7, 'en', 'Chipset', ''),
(15, 8, 'ru', 'Оперативная память (RAM)', ''),
(16, 8, 'en', 'RAM', ''),
(17, 9, 'ru', 'Встроенная память', ''),
(18, 9, 'en', 'Internal', ''),
(19, 10, 'ru', 'Карта памяти (максиум)', ''),
(20, 10, 'en', 'Memory card (maximum)', ''),
(21, 11, 'ru', 'Тип', ''),
(22, 11, 'en', 'Type', ''),
(23, 12, 'ru', 'Разрешение', ''),
(24, 12, 'en', 'Resolution', ''),
(25, 13, 'ru', 'Диагональ (дюймы)', ''),
(26, 13, 'en', 'Size (inches)', ''),
(27, 14, 'ru', 'Наименование', ''),
(28, 14, 'en', 'Name', ''),
(29, 15, 'ru', 'Стандарты GSM', ''),
(30, 15, 'en', 'GSM networks', ''),
(31, 16, 'ru', 'Защита', ''),
(32, 16, 'en', 'Protetction', ''),
(33, 17, 'ru', 'SIM', ''),
(34, 17, 'en', 'SIM', ''),
(35, 18, 'ru', 'Wi-Fi', ''),
(36, 18, 'en', 'Wi-Fi', ''),
(37, 19, 'ru', 'Bluetooth', ''),
(38, 19, 'en', 'Bluetooth', ''),
(39, 20, 'ru', 'Инфракрасный порт (IRDA)', ''),
(40, 20, 'en', 'Infrared port (IRDA)', ''),
(41, 21, 'ru', 'GPS навигация', ''),
(42, 21, 'en', 'GPS navigation', ''),
(43, 22, 'ru', 'Емкость', ''),
(44, 22, 'en', 'Сapacity', ''),
(45, 23, 'ru', 'Тип', ''),
(46, 23, 'en', 'Type', ''),
(47, 24, 'ru', 'Время работы в режиме ожидания', ''),
(48, 24, 'en', 'Stand-by', ''),
(49, 25, 'ru', 'Время работы в режиме разговора', ''),
(50, 25, 'en', 'Talk time', ''),
(51, 26, 'ru', 'Разрешение', ''),
(52, 26, 'en', 'Resolution', ''),
(53, 27, 'ru', 'Разрешение в мегапикселах', ''),
(54, 27, 'en', 'Resolution in megapixels', ''),
(55, 28, 'ru', 'Вспышка', ''),
(56, 28, 'en', 'Flash', ''),
(57, 29, 'ru', 'Дополнительная камера', ''),
(58, 29, 'en', 'Secondary camera', ''),
(59, 30, 'ru', '', ''),
(60, 30, 'en', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `ai_faq`
--

CREATE TABLE IF NOT EXISTS `ai_faq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL,
  `lang` varchar(4) NOT NULL DEFAULT 'ru',
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `source` varchar(255) NOT NULL COMMENT 'Ссылка на оригинал для автоматического заполнения',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_files`
--

CREATE TABLE IF NOT EXISTS `ai_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods`
--

CREATE TABLE IF NOT EXISTS `ai_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `link` varchar(100) NOT NULL,
  `type` int(11) NOT NULL,
  `brand` int(11) NOT NULL,
  `is_modification` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Если модификация, отображаем только в карточке товара с комментарием',
  `temp_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_type_name` (`brand`,`name`,`type`),
  UNIQUE KEY `link` (`link`),
  UNIQUE KEY `brand_type_link` (`type`,`link`),
  KEY `type` (`type`),
  KEY `brand` (`brand`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `ai_goods`
--

INSERT INTO `ai_goods` (`id`, `name`, `model`, `link`, `type`, `brand`, `is_modification`, `temp_id`) VALUES
(1, 'X', '', 'x', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_characteristics`
--

CREATE TABLE IF NOT EXISTS `ai_goods_characteristics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL,
  `characteristic` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  `lang` varchar(4) NOT NULL DEFAULT 'any',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`,`characteristic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_files`
--

CREATE TABLE IF NOT EXISTS `ai_goods_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL,
  `file` int(10) unsigned NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`,`file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Файлы для товаров (прошивки, мануалы)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_files_descriptions`
--

CREATE TABLE IF NOT EXISTS `ai_goods_files_descriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` int(10) unsigned NOT NULL COMMENT 'id из goods_files',
  `lang` varchar(4) NOT NULL DEFAULT 'ru',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file` (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Описания файлов, привязанных к товарам' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_images`
--

CREATE TABLE IF NOT EXISTS `ai_goods_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL,
  `image` int(10) unsigned NOT NULL,
  `priority` int(11) DEFAULT '100' COMMENT 'Порядок сортировки (DESC)',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_modifications`
--

CREATE TABLE IF NOT EXISTS `ai_goods_modifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_parent` int(10) unsigned NOT NULL COMMENT 'Базовая модель',
  `goods_children` int(10) unsigned NOT NULL COMMENT 'Модель - модицикация',
  PRIMARY KEY (`id`),
  KEY `goods_parent` (`goods_parent`,`goods_children`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Модификации товаров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_synonims`
--

CREATE TABLE IF NOT EXISTS `ai_goods_synonims` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL COMMENT 'ID товара',
  `name` varchar(255) NOT NULL COMMENT 'Сам синоним',
  `visibled` tinyint(1) DEFAULT '1' COMMENT 'Показывать на странице (если нет, то используем только в технических целях)',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Синонимы к названиям товаров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_types`
--

CREATE TABLE IF NOT EXISTS `ai_goods_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link` (`link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Типы товаров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_types_names`
--

CREATE TABLE IF NOT EXISTS `ai_goods_types_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL,
  `lang` varchar(4) NOT NULL DEFAULT 'ru',
  `name` varchar(255) NOT NULL,
  `video_search_string` varchar(255) NOT NULL DEFAULT '%s %s' COMMENT 'Строка printf для поиска',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Переводы типов товаров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_images`
--

CREATE TABLE IF NOT EXISTS `ai_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` int(10) unsigned NOT NULL COMMENT 'ID файла',
  `size` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Размер (константа)',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `file` (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_images_resized`
--

CREATE TABLE IF NOT EXISTS `ai_images_resized` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` int(11) unsigned NOT NULL COMMENT 'ID изображения',
  `size` tinyint(4) NOT NULL COMMENT 'Размер (константа)',
  `file` int(11) unsigned NOT NULL COMMENT 'ID файла',
  `width` int(11) unsigned NOT NULL DEFAULT '0',
  `height` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица миниатюр изображений' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_modifications_comments`
--

CREATE TABLE IF NOT EXISTS `ai_modifications_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modification` int(10) unsigned NOT NULL COMMENT 'ID из таблицы goods_modifications',
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `comment` varchar(255) NOT NULL COMMENT 'Комментарий к модицикации товара',
  PRIMARY KEY (`id`),
  KEY `modification` (`modification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_ratings_comments`
--

CREATE TABLE IF NOT EXISTS `ai_ratings_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment` int(10) unsigned NOT NULL COMMENT 'Комментарий',
  `user` int(10) unsigned NOT NULL COMMENT 'Пользователь',
  `value` tinyint(3) unsigned NOT NULL COMMENT 'Голос',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Когда проголосовали',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки комментариев' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_ratings_faq`
--

CREATE TABLE IF NOT EXISTS `ai_ratings_faq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `faq` int(10) unsigned NOT NULL COMMENT 'Вопрос',
  `user` int(10) unsigned NOT NULL COMMENT 'Пользователь',
  `value` tinyint(3) unsigned NOT NULL COMMENT 'Голос',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Когда проголосовали',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки вопросов' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_ratings_goods`
--

CREATE TABLE IF NOT EXISTS `ai_ratings_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL COMMENT 'Товар',
  `user` int(10) unsigned NOT NULL COMMENT 'Пользователь',
  `value` tinyint(3) unsigned NOT NULL COMMENT 'Голос',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Когда проголосовали',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки товаров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_ratings_reviews`
--

CREATE TABLE IF NOT EXISTS `ai_ratings_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `review` int(10) unsigned NOT NULL COMMENT 'Обзор',
  `user` int(10) unsigned NOT NULL COMMENT 'Пользователь',
  `value` tinyint(3) unsigned NOT NULL COMMENT 'Голос',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Когда проголосовали',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки обзоров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_ratings_videos`
--

CREATE TABLE IF NOT EXISTS `ai_ratings_videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `video` int(10) unsigned NOT NULL COMMENT 'Видео',
  `user` int(10) unsigned NOT NULL COMMENT 'Пользователь',
  `value` tinyint(3) unsigned NOT NULL COMMENT 'Голос',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Когда проголосовали',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки видео' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_reviews`
--

CREATE TABLE IF NOT EXISTS `ai_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL COMMENT 'ID товара',
  `link` varchar(150) NOT NULL COMMENT 'Ссылка на обзор (возможно, не нужна)',
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `author` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'id пользователя - автора обзора',
  `title` varchar(255) NOT NULL COMMENT 'Заголовок',
  `content` text NOT NULL COMMENT 'Сам отзыв',
  `priority` tinyint(4) NOT NULL DEFAULT '100' COMMENT 'Приоритет сортировки (в дальнейшем буду использовать рейтинги)',
  `source` varchar(255) NOT NULL COMMENT 'Источник (ссылка на оригинал для автоматического добавления новых)',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Не отображать',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`),
  KEY `author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Обзоры' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_sources_gsmarena`
--

CREATE TABLE IF NOT EXISTS `ai_sources_gsmarena` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `file` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `file` (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_sources_gsmarena_files`
--

CREATE TABLE IF NOT EXISTS `ai_sources_gsmarena_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_users`
--

CREATE TABLE IF NOT EXISTS `ai_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL COMMENT 'Логин (обычно email, логин обычному пользователю задать нельзя, только для модераторов и админов)',
  `password` varchar(36) NOT NULL COMMENT 'хэш пароля с солью',
  `email` varchar(200) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Имя, пример Иван Петров',
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `avatar` int(11) NOT NULL DEFAULT '0' COMMENT 'id изображения - аватара',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Последнее изменение',
  `role` varchar(50) NOT NULL DEFAULT 'user' COMMENT 'Роль (пользователь, модератор, админ, etc)',
  `readonly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Если флудит и спамит, можно заткнуть рот',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Подтверждение email',
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Последний вход',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_videos`
--

CREATE TABLE IF NOT EXISTS `ai_videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL COMMENT 'ID товара',
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT 'Тип видео (ютуб, vimeo, etc)',
  `link` varchar(100) NOT NULL COMMENT 'Код видео для вставки в шаблон',
  `priority` int(11) NOT NULL DEFAULT '100' COMMENT 'Порядок сортировки (DESC)',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Не отображать на странице (используется как фильтр для автоматически подгружаемых видео)',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Видео для товаров' AUTO_INCREMENT=1 ;
