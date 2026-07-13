<?php
/**
 * Adds the `llm` column (content for /auto/{category}/{title}/llm.txt).
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($module)
{
    return Db::getInstance()->execute(
        'ALTER TABLE `' . _DB_PREFIX_ . 'auto_page_lang`
        ADD COLUMN `llm` MEDIUMTEXT AFTER `content`'
    );
}
