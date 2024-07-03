<?php

namespace ShopEngine\Core\Wc_Customizer;

use ShopEngine\Traits\Singleton;

defined('ABSPATH') || exit;

/**
 * Description: this class will register option in wordpress default customizer
 *
 * @package    ShopEngine
 * @subpackage ShopEngine/Core/Wc_Customizer
 * @since      4.5.0
 */
class Register_Settings
{
    use Singleton;

    /**
     * will int the class
     *
     * @since      4.5.0
     * @access     public
     * @return     void
     */
    public function init(){
        add_action('customize_register', [$this, 'shopengine_add_customizer_options']);
    }

    /**
     * Add customizer options
     *
     * This is a callback function for customize_register action. It'll add a customizer option in the Customizer > WooCommerce > Product Catalog section
     * @since      4.5.0
     * @access     public
     * @param      object $wp_customize
     * @return     void
     */
   public function shopengine_add_customizer_options( $wp_customize ) {
       
       $wp_customize->add_setting('shopengine_product_per_page_mobile', array(
           'default' => '2',
           'sanitize_callback' => 'sanitize_text_field',
       ));
   
       $wp_customize->add_control('shopengine_product_per_page_mobile', array(
           'label' => __('Products per row in mobile', 'shopengine'),
           'section' => 'woocommerce_product_catalog', 
           'type' => 'number',
           'priority' => 11,
           'input_attrs' => array(
               'min' => 1,
               'step' => 1,
           ),
       ));

       $wp_customize->add_setting('shopengine_product_per_page_tablet', array(
        'default' => '2',
        'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('shopengine_product_per_page_tablet', array(
            'label' => __('Products per row in tablet', 'shopengine'),
            'section' => 'woocommerce_product_catalog', 
            'type' => 'number',
            'priority' => 11,
            'input_attrs' => array(
                'min' => 1,
                'step' => 1,
            ),
        ));
   }
   
}
