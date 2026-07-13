<?php
/**
 * Page entity for the Auto module, structurally mirroring CMS.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoPage extends ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_auto_category;

    /** @var bool */
    public $active = true;

    /** @var int */
    public $position;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    /** @var string|array */
    public $title;

    /** @var string|array */
    public $link_rewrite;

    /** @var string|array */
    public $meta_title;

    /** @var string|array */
    public $meta_description;

    /** @var string|array */
    public $meta_keywords;

    /** @var string|array */
    public $content;

    /** @var string|array Plain-text content served at /auto/{category}/{title}/llm.txt */
    public $llm;

    public static $definition = [
        'table' => 'auto_page',
        'primary' => 'id_auto_page',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'id_auto_category' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'position' => ['type' => self::TYPE_INT],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            /* Lang fields */
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128],
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512],
            'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4194303],
            'llm' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4194303],
        ],
    ];

    public function add($autoDate = true, $nullValues = false)
    {
        $this->position = (int) AutoPage::getLastPosition((int) $this->id_auto_category);

        return parent::add($autoDate, $nullValues);
    }

    public function delete()
    {
        $idCategory = (int) $this->id_auto_category;
        $result = parent::delete();
        AutoPage::cleanPositions($idCategory);

        return $result;
    }

    public static function getLastPosition($idAutoCategory)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT MAX(position) + 1 FROM `' . _DB_PREFIX_ . 'auto_page` WHERE `id_auto_category` = ' . (int) $idAutoCategory
        );
    }

    public static function cleanPositions($idAutoCategory)
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id_auto_page` FROM `' . _DB_PREFIX_ . 'auto_page`
            WHERE `id_auto_category` = ' . (int) $idAutoCategory . '
            ORDER BY `position`'
        );

        foreach ($result as $i => $row) {
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'auto_page`
                SET `position` = ' . (int) $i . '
                WHERE `id_auto_page` = ' . (int) $row['id_auto_page']
            );
        }

        return true;
    }

    /**
     * @param string $linkRewrite
     * @param int $idAutoCategory
     * @param int $idLang
     * @param int|null $idShop
     *
     * @return AutoPage|false
     */
    public static function getByLinkRewrite($linkRewrite, $idAutoCategory, $idLang, $idShop = null)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        $sql = 'SELECT p.`id_auto_page`
            FROM `' . _DB_PREFIX_ . 'auto_page` p
            INNER JOIN `' . _DB_PREFIX_ . 'auto_page_lang` pl
                ON (pl.`id_auto_page` = p.`id_auto_page` AND pl.`id_lang` = ' . (int) $idLang . ' AND pl.`id_shop` = ' . (int) $idShop . ')
            INNER JOIN `' . _DB_PREFIX_ . 'auto_page_shop` ps
                ON (ps.`id_auto_page` = p.`id_auto_page` AND ps.`id_shop` = ' . (int) $idShop . ')
            WHERE pl.`link_rewrite` = \'' . pSQL($linkRewrite) . '\'
            AND p.`id_auto_category` = ' . (int) $idAutoCategory . '
            AND p.`active` = 1';

        $idAutoPage = (int) Db::getInstance()->getValue($sql);

        if (!$idAutoPage) {
            return false;
        }

        return new AutoPage($idAutoPage, $idLang, $idShop);
    }

    /**
     * @param int $idAutoCategory
     * @param int|null $idLang
     * @param bool $active
     *
     * @return array
     */
    public static function getPagesByCategory($idAutoCategory, $idLang = null, $active = true)
    {
        if (!$idLang) {
            $idLang = (int) Context::getContext()->language->id;
        }
        $idShop = (int) Context::getContext()->shop->id;

        $sql = 'SELECT p.`id_auto_page`, pl.`title`, pl.`link_rewrite`, pl.`meta_title`
            FROM `' . _DB_PREFIX_ . 'auto_page` p
            INNER JOIN `' . _DB_PREFIX_ . 'auto_page_lang` pl
                ON (pl.`id_auto_page` = p.`id_auto_page` AND pl.`id_lang` = ' . (int) $idLang . ' AND pl.`id_shop` = ' . (int) $idShop . ')
            WHERE p.`id_auto_category` = ' . (int) $idAutoCategory .
            ($active ? ' AND p.`active` = 1' : '') . '
            ORDER BY p.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }
}
