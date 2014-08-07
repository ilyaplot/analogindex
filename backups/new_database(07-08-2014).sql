-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Авг 07 2014 г., 18:01
-- Версия сервера: 5.6.16
-- Версия PHP: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
-- Структура таблицы `ai_characteristics`
--

CREATE TABLE IF NOT EXISTS `ai_characteristics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit` int(11) NOT NULL DEFAULT '0' COMMENT 'Единица измерения из таблицы units_names',
  `factor` int(11) NOT NULL DEFAULT '0' COMMENT 'Множитель из таблицы factors. Если 0, считаем автоматически',
  `catalog` int(11) NOT NULL DEFAULT '0' COMMENT 'id из таблицы characteristics_catalogs',
  `parent` int(11) NOT NULL DEFAULT '0' COMMENT 'id родительской категории (0, если родитель)',
  `priority` int(11) NOT NULL DEFAULT '100' COMMENT 'Порядок сортировки (DESC)',
  PRIMARY KEY (`id`),
  KEY `type` (`unit`,`catalog`,`parent`),
  KEY `factor` (`factor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Характеристики товаров' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_characteristics_catalogs`
--

CREATE TABLE IF NOT EXISTS `ai_characteristics_catalogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority` int(11) NOT NULL DEFAULT '100' COMMENT 'Приоритет сортировки (desc)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Категории характеристик' AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Названия каталогов характеристик (мультиязычность)' AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Названия характеристик' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_factors`
--

CREATE TABLE IF NOT EXISTS `ai_factors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` int(10) unsigned NOT NULL DEFAULT '1000' COMMENT 'Множитель для преобразования в кило, мега, гига, etc',
  `is_double` tinyint(1) NOT NULL COMMENT 'Может ли являться дробным',
  `is_unsigned` tinyint(1) NOT NULL COMMENT 'Может ли быть отрицательным',
  `default_factor` varchar(255) NOT NULL COMMENT 'Множитель по умолчанию (кило, мега, гига, etc)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Множители' AUTO_INCREMENT=1 ;

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
  `link` varchar(100) NOT NULL,
  `type` int(11) NOT NULL,
  `brand` int(11) NOT NULL,
  `is_modification` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Если модификация, отображаем только в карточке товара с комментарием',
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

INSERT INTO `ai_goods` (`id`, `name`, `link`, `type`, `brand`, `is_modification`) VALUES
(1, 'X', 'x', 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `ai_goods_characteristics`
--

CREATE TABLE IF NOT EXISTS `ai_goods_characteristics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods` int(10) unsigned NOT NULL,
  `characteristic` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
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
  `disabled` int(11) NOT NULL DEFAULT '0' COMMENT 'Не отображать',
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
-- Структура таблицы `ai_units_names`
--

CREATE TABLE IF NOT EXISTS `ai_units_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(4) NOT NULL DEFAULT 'ru' COMMENT 'Код языка',
  `full` varchar(100) NOT NULL COMMENT 'Полное название',
  `short` varchar(10) NOT NULL COMMENT 'Короткое название',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Названия множителей' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ai_users`
--

CREATE TABLE IF NOT EXISTS `ai_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL COMMENT 'Логин (обычно email, логин обычному пользователю задать нельзя, только для модераторов и админов)',
  `password` int(36) NOT NULL COMMENT 'хэш пароля с солью',
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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
