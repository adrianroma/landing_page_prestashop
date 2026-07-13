<?php
/**
 * Category entity for the Auto module, structurally mirroring CMSCategory.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoCategory extends ObjectModel
{
    /** @var int */
    public $id;

    /** @var bool */
    public $active = true;

    /** @var int */
    public $position;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    /** @var string|array */
    public $name;

    /** @var string|array */
    public $link_rewrite;

    /** @var string|array */
    public $meta_title;

    /** @var string|array */
    public $meta_description;

    /** @var string|array */
    public $meta_keywords;

    /** @var string|array */
    public $description;

    public static $definition = [
        'table' => 'auto_category',
        'primary' => 'id_auto_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'position' => ['type' => self::TYPE_INT],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 255],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128],
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512],
            'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
            'description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4194303],
        ],
    ];

    public function add($autoDate = true, $nullValues = false)
    {
        $this->position = (int) AutoCategory::getLastPosition();

        return parent::add($autoDate, $nullValues);
    }

    public function update($nullValues = false)
    {
        return parent::update($nullValues);
    }

    public function delete()
    {
        foreach (AutoPage::getPagesByCategory((int) $this->id, null, false) as $page) {
            $autoPage = new AutoPage($page['id_auto_page']);
            $autoPage->delete();
        }

        $result = parent::delete();
        AutoCategory::cleanPositions();

        return $result;
    }

    public static function getLastPosition()
    {
        return (int) Db::getInstance()->getValue(
            'SELECT MAX(position) + 1 FROM `' . _DB_PREFIX_ . 'auto_category`'
        );
    }

    public static function cleanPositions()
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id_auto_category` FROM `' . _DB_PREFIX_ . 'auto_category` ORDER BY `position`'
        );

        foreach ($result as $i => $row) {
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'auto_category`
                SET `position` = ' . (int) $i . '
                WHERE `id_auto_category` = ' . (int) $row['id_auto_category']
            );
        }

        return true;
    }

    /**
     * @param string $linkRewrite
     * @param int $idLang
     * @param int|null $idShop
     *
     * @return array|false
     */
    public static function getByLinkRewrite($linkRewrite, $idLang, $idShop = null)
    {
        if (!$idShop) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        $sql = 'SELECT c.`id_auto_category`
            FROM `' . _DB_PREFIX_ . 'auto_category` c
            INNER JOIN `' . _DB_PREFIX_ . 'auto_category_lang` cl
                ON (cl.`id_auto_category` = c.`id_auto_category` AND cl.`id_lang` = ' . (int) $idLang . ' AND cl.`id_shop` = ' . (int) $idShop . ')
            INNER JOIN `' . _DB_PREFIX_ . 'auto_category_shop` cs
                ON (cs.`id_auto_category` = c.`id_auto_category` AND cs.`id_shop` = ' . (int) $idShop . ')
            WHERE cl.`link_rewrite` = \'' . pSQL($linkRewrite) . '\'
            AND c.`active` = 1';

        $idAutoCategory = (int) Db::getInstance()->getValue($sql);

        if (!$idAutoCategory) {
            return false;
        }

        return new AutoCategory($idAutoCategory, $idLang, $idShop);
    }

    /**
     * @param int $idLang
     * @param bool $active
     *
     * @return array
     */
    public static function getCategories($idLang, $active = true)
    {
        $idShop = (int) Context::getContext()->shop->id;

        $sql = 'SELECT c.`id_auto_category`, cl.`name`, cl.`link_rewrite`, cl.`meta_title`, cl.`description`
            FROM `' . _DB_PREFIX_ . 'auto_category` c
            INNER JOIN `' . _DB_PREFIX_ . 'auto_category_lang` cl
                ON (cl.`id_auto_category` = c.`id_auto_category` AND cl.`id_lang` = ' . (int) $idLang . ' AND cl.`id_shop` = ' . (int) $idShop . ')
            INNER JOIN `' . _DB_PREFIX_ . 'auto_category_shop` cs
                ON (cs.`id_auto_category` = c.`id_auto_category` AND cs.`id_shop` = ' . (int) $idShop . ')' .
            ($active ? ' WHERE c.`active` = 1' : '') . '
            ORDER BY c.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }
}
