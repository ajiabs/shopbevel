<?php
class Namespace_Module_Model_Resource_Eav_Mysql4_Setup
    extends Mage_Eav_Model_Entity_Setup
{
    //  {{{ getDefaultEntities()

    /**
     * Install a new entity
     *
     * It is very important to set the group index value.
     * If you don't - you will not see the attribute in the form in admin
     *
     * Also the attribute array index columns are defined quiet differently
     * between magento versions 1.4 - 1.5 and 1.6
     *
     * In versions 1.4 - 1.5 look at the file
     * app/code/core/Mage/Catalog/Model/Resource/Eav/Mysql4/Setup.php
     * to get an idea of all the indexes that can be defined
     *
     * In versions 1.6+ look at the file
     * app/code/core/Mage/Catalog/Model/Resource/Setup.php
     *
     *
     * @return array entities to install
     * @access public
     */
    public function getDefaultEntities()
    {
        return array(
            'catalog_category' => array(
                'entity_model'      => 'catalog/category',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/category',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/category_attribute_collection',
                'attributes' => array(
                    'attribute_name' => array(
                        'type'              => 'varchar(75)',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Designer Name',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => 'eav/entity_attribute_source_boolean',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'unique'            => false,
                        /**
                         * Need the group index in this array otherwise
                         * the attribute will not appear in the admin form.
                         *
                         * This group name matches the tabs you see in the
                         * admin section - and is case sensitive.
                         *
                         * In versions 1.4 - 1.5, the general tab is a
                         * lowercase 'general', but in 1.6+ it switched
                         * to 'General Information'.   Just look carefully
                         * and you will be alright

                         *
                         * 'group' => 'general' // v1.4-v1.5
                         * 'group' => 'General Information' // v1.6+
                         * 'group' => 'Display Settings'
                         * 'group' => 'Custom Design'
                         *
                         * You can also easily create new tabs by defining
                         * a custom group here
                         *
                         * 'group' => 'New Group Tab Name'
                         */
                        'group'             => 'general',
                    ),
                ),
            ),
        );
    }

    //  }}}
}