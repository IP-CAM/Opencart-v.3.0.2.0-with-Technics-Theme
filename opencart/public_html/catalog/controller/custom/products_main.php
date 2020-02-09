<?php
class ControllerCustomProductsMain extends Controller {
	public function index() {
		$this->load->model('design/layout');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		$filter = array(
		    'limit' => 9,
            'start' => 0
        );
		$allProducts = $this->model_catalog_product->getProducts($filter);

		$prodCats = array(); // <-- массив "имя товара" => "категории к которым он относится"

		foreach ($allProducts as $prod) {
		    $prodCats[$prod['name']] = $this->model_catalog_product->getCategories($prod['product_id']);
        }

		$cats = $this->model_catalog_category->getCategories();

		$cNames = array();
		foreach ($cats as $cat) {
		    $cNames[] = $cat['name'];
        }

		$data['products'] = $allProducts;
		$data['cat_names'] = $cNames;

		return $this->load->view('custom/products_main', $data);
	}
}
