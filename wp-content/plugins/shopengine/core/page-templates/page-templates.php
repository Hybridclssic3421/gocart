<?php

namespace ShopEngine\Core\PageTemplates;

defined('ABSPATH') || exit;

use ShopEngine\Traits\Singleton;
use ShopEngine\Widgets\Products;


class Page_Templates {
	use Singleton;

	private $templateList = [];
	private $listedCollected = false;

	public function init() {

		add_filter('elementor/document/urls/edit', function($url) {
			if(is_single()) {
				global $wp;
				$query   = $wp->query_vars;
				if(isset($query['name'])){
					$product = get_page_by_path( $query['name'], OBJECT, 'product' );
					if(!empty($product->ID)) {
						return $url . "&shopengine_product_id=" . $product->ID;
					}
				}
			}
			return $url;
		});

		$templates = $this->getTemplates();

		foreach($templates as $key => $template) {

			if(isset($template['class']) && $template['class']) {

				new $template['class']();

			}
		}
	}


	public function getTemplates() {

		if(!$this->listedCollected) {
			$this->templateList    = apply_filters('shopengine/page_templates', $this->get_list());
			$this->listedCollected = true;
		}

		return $this->templateList;
	}

	public function getTemplate($slug) {
		$page_templates = $this->getTemplates();

		return $page_templates[$slug] ?? [];
	}


	public function get_list() {

		$product_id = Products::instance()->get_preview_product();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Some other templates call it without nonce added.
		if(isset($_GET['shopengine_product_id'])) { 
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Some other templates call it without nonce added.
			$product_id = sanitize_text_field(wp_unslash($_GET['shopengine_product_id']));
			update_option('__shopengine_preview_product_id', $product_id);
		} elseif(get_option('__shopengine_preview_product_id')) {
			$product_id = get_option('__shopengine_preview_product_id');
		}

		$shop_url = get_permalink(wc_get_page_id('shop'));
		$shop_url = (strpos($shop_url, '?page_id') !== false ? get_home_url() . '?post_type=product' : $shop_url);

		return [
			'shop'     => [
				'title'   => esc_html__('Shop', 'shopengine'),
				'package' => 'free',
				'class'   => 'ShopEngine\Core\Page_Templates\Hooks\Shop',
				'opt_key' => 'shop',
				'css'     => 'shop',
				'url'     => $shop_url,
			],
			'archive'  => [
				'title'   => esc_html__('Archive', 'shopengine'),
				'package' => 'free',
				'class'   => 'ShopEngine\Core\Page_Templates\Hooks\Archive',
				'opt_key' => 'archive',
				'css'     => 'archive',
				'url'     => $shop_url,
			],
			'single'   => [
				'title'   => esc_html__('Single', 'shopengine'),
				'package' => 'free',
				'class'   => 'ShopEngine\Core\Page_Templates\Hooks\Single',
				'opt_key' => 'single',
				'css'     => 'single',
				'url'     => get_permalink($product_id),
			],
			'cart'     => [
				'title'   => esc_html__('Cart', 'shopengine'),
				'package' => 'free',
				'class'   => 'ShopEngine\Core\Page_Templates\Hooks\Cart',
				'opt_key' => 'cart',
				'css'     => 'cart',
				'url'     => get_permalink(wc_get_page_id('cart')),
			],
			'checkout' => [
				'title'   => esc_html__('Checkout', 'shopengine'),
				'package' => 'free',
				'class'   => 'ShopEngine\Core\Page_Templates\Hooks\Checkout',
				'opt_key' => 'checkout',
				'css'     => 'checkout',
				'url'     => get_permalink(wc_get_page_id('checkout')),
			],
		];


	}
}
