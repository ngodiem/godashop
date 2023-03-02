<?php 
class HomeController {
	function list() {
		$page = 1; // lấy trang đầu tiên
		$item_per_page = 4; // 4 sản phẩm trên 1 trang
		$productRepository = new ProductRepository();
		$conds = [];
		$sorts = ["featured" => "DESC"]; // sắp xếp cột trong featured giảm dần
		$featuredProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
		//$featuredProducts: sản phẩm nổi bật sắp xếp theo
		// echo count($featuredProducts);
		// echo "đây là trang chủ";
		// $sconds lấy sp theo đk nào(nổi bật), $sorts sắp xếp sp theo đk nào

		
		$sorts = ["created_date" => "DESC"]; // sắp xếp cột trong creat_date giảm 
		$latestProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
		// $latestProducts: sản phẩm mới nhất đk sắp xếp theo ngày tạo DESC


		// lấy sản phẩm trong danh mục 
		$categoryRepository =  new CategoryRepository();
		$categories = $categoryRepository->getAll();
		$categoryProduct = [];
		foreach ($categories as $category) {
			// tìm kiếm theo cột category_id
			$conds = [
				"category_id" => [
					"type" => "=", // tìm sản phẩm so sánh theo kiểu bằng
					"val"  => $category->getId()
				]
			];

			$products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);	
			$categoryProducts[$category->getName()] = $products;
		}
		// echo count($categoryProducts);

		require ABSPATH_SITE . "view/home/list.php";	}
	}

	?>