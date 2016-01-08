Bidorbuy XML feed plugin - Tested on Opencart 2.0.1.1
Version - 1.0
Author - Jason Bolton - idksa - idkdevsa@gmail.com

<?php
class ControllerFeedbobfeedoc2011 extends Controller {
	public function index() {
		
			$output  = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';

			$this->load->model('catalog/category');

			$this->load->model('catalog/product');

			$this->load->model('tool/image');

			$products = $this->model_catalog_product->getProducts();
			
			// build xml output
			
			$output .= '<ROOT>';
			$output .= '<Products>';
			
			foreach ($products as $product) {
				if ($product['description']) {
					$output .= '<Product>';
					$output .= '<ProductCode><![CDATA['.$product['model'].']]></ProductCode>';
					$output .= '<Title><![CDATA['.$product['name'].']]></Title>';
					$categories = $this->model_catalog_product->getCategories($product['product_id']);

					foreach ($categories as $category) {
						$path = $this->getPath($category['category_id']);

						if ($path) {
							$string = '';

							foreach (explode('_', $path) as $path_id) {
								$category_info = $this->model_catalog_category->getCategory($path_id);

								if ($category_info) {
									if (!$string) {
										$string = $category_info['name'];
									} else {
										$string .= ' &gt; ' . $category_info['name'];
									}
								}
							}

							$output .= '<Category><![CDATA['.$string.']]></Category>';
						}
					}
					
					$output .= '<Price>' . $product['price'] . '</Price>';
					$output .= '<ImageURL><![CDATA['.$product['image'].']]></ImageURL>';
					$output .= '<Description><![CDATA['.utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'))).']]></Description>';
					$output .= '</Product>';
				}
			}

			$output .= '</Products>';
			$output .= '</ROOT>';
			
			$this->response->addHeader('Content-Type: application/rss+xml');
			$this->response->setOutput($output);
		}
	
			//end block

			
	protected function getPath($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id);

		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}

			$path = $this->getPath($category_info['parent_id'], $new_path);

			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}
}
?>