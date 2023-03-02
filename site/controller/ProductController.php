<?php 
class ProductController {
	function list() {
		$productRepository = new ProductRepository();
		$conds = [];
		$sorts = [];
		$page = !empty($_GET["page"]) ? $_GET["page"] : 1;
		$item_per_page = 4;
// danh mục sp
 $category_id = !empty($_GET["category_id"]) ? $_GET["category_id"] : null;
 $categoryName = "tất cả sản phẩm";
		if(!empty($category_id)) {
			$conds = [
				"category_id" => [
					"type" => "=",
					"val"  => $category_id
				]
			];
		$categoryRepository = new CategoryRepository();
		$category = $categoryRepository->find($category_id);
		$categoryName = $category->getName();
		}

// tìm kiếm theo tên
		 $search = !empty($_GET["search"]) ? $_GET["search"] : null;
		if(!empty($search)) {
			$conds = [
				"name" => [
					"type" => "LIKE",
					"val"  => "'%$search%'"
				]
			];
		}
// khoản giá
	$price_range = !empty($_GET["price-range"]) ? $_GET["price-range"] : null;
		if(!empty($price_range)) {
			// SELECT * FROM view_product WHERE $price_range BETWEEN 100000 AND 200000
			$temp = explode("-", $price_range);
			$start = $temp[0];
			$end  = $temp[1];

			$conds = [
				"sale_price" => [
					"type" => "BETWEEN",
					"val"  => "$start AND $end"
				]
			];

			if($end == "greater") {
				$conds = [
				"sale_price" => [
					"type" => ">=",
					"val"  => $start
				]
			];
			}
		}

		$sort = !empty($_GET["sort"]) ? $_GET["sort"] : null;
		if(!empty($sort)) {

		$temp = explode("-", $sort);
		$name = $temp[0];
		$orderType = $temp[1];
		$map = ["price" => "sale_price", "alpha" => "name" , "created" =>"created_date"];

		$column = $map[$name];

		$sorts = [$column => $orderType];

		}
	// Phân trang
		$totalProducts = $productRepository->getBy($conds, $sorts);
		$totalPage = ceil(count($totalProducts) / $item_per_page);

		$products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);

		// tạo danh mục sp
		$categoryRepository =  new CategoryRepository();
		$categories = $categoryRepository->getAll();		// echo "đây là trang sản phẩm";
		require ABSPATH_SITE . "view/product/list.php";
	}
	function ajaxSearch() {
		// echo "haha";
		$conds = [];
		$sorts = [];
		$search = !empty($_GET["pattern"]) ? $_GET["pattern"] : null;
		if(!empty($search)) {
			$conds = [
				"name" => [
					"type" => "LIKE",
					"val"  => "'%$search%'"
				]
			];
		}
		$productRepository = new ProductRepository();
		$products = $productRepository->getBy($conds, $sorts);
		require ABSPATH_SITE . "view/product/ajaxSearch.php";
	}
	function detail(){
		$id = $_GET["id"];
		$productRepository = new ProductRepository;
		$product = $productRepository->find($id);
		$category_id = $product->getCategoryId();
		$price_range = null;
		$categoryName = $product->getCategory()->getName();

		$categoryRepository =  new CategoryRepository();
		$categories = $categoryRepository->getAll();

		
		$sorts = [];
		$conds = [
			"category_id" => [
			"type" => "=",
			"val" => $category_id
			],
			"id" => [
				"type" => "!=",
				"val" => $id
				]
			
		];
		$relatedProducts = $productRepository->getBy($conds, $sorts);
		require ABSPATH_SITE . "view/product/detail.php";
	}
}

 ?>