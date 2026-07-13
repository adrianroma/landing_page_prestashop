<?php
/**
 * Database schema for the Auto module.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'auto_category` (
    `id_auto_category` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_auto_category`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'auto_category_lang` (
    `id_auto_category` INT UNSIGNED NOT NULL,
    `id_lang` INT UNSIGNED NOT NULL,
    `id_shop` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `link_rewrite` VARCHAR(128) NOT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` VARCHAR(512) DEFAULT NULL,
    `meta_keywords` VARCHAR(255) DEFAULT NULL,
    `description` MEDIUMTEXT,
    PRIMARY KEY (`id_auto_category`, `id_lang`, `id_shop`),
    KEY `idx_auto_category_lang_rewrite` (`link_rewrite`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'auto_category_shop` (
    `id_auto_category` INT UNSIGNED NOT NULL,
    `id_shop` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id_auto_category`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'auto_page` (
    `id_auto_page` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_auto_category` INT UNSIGNED NOT NULL,
    `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_auto_page`),
    KEY `idx_auto_page_category` (`id_auto_category`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'auto_page_lang` (
    `id_auto_page` INT UNSIGNED NOT NULL,
    `id_lang` INT UNSIGNED NOT NULL,
    `id_shop` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `link_rewrite` VARCHAR(128) NOT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` VARCHAR(512) DEFAULT NULL,
    `meta_keywords` VARCHAR(255) DEFAULT NULL,
    `content` MEDIUMTEXT,
    `llm` MEDIUMTEXT,
    PRIMARY KEY (`id_auto_page`, `id_lang`, `id_shop`),
    KEY `idx_auto_page_lang_rewrite` (`link_rewrite`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'auto_page_shop` (
    `id_auto_page` INT UNSIGNED NOT NULL,
    `id_shop` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id_auto_page`, `id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
