<?php 
class CartController {
	protected $cartStorage;
	function __construct() {
		$this->cartStorage = new CartStorage();
	}
	function display() {
		$cart = $this->cartStorage->fetch(); // catsStorage thao tác trên file
		// bản chất cooke là file thôi
		echo json_encode($cart->convertToArray()); // trả về array rổng, chuyển về chuổi
	}

	function add() {
		$product_id = $_GET["product_id"]; // server lấy product_id = 2
		$qty = $_GET["qty"]; // qty=1;
		$cart = $this->cartStorage->fetch(); // lấy giỏ hàng ra(lần đầu 0)
		// lần 2 mua->fetch();
		$cart->addProduct($product_id, $qty); // add 1 sp vào

		$this->cartStorage->store($cart); 
		//Đổi đối tượng -> chuỗi
		//Đổi tượng thành array, sau đó từ array -> chuỗi
		// echo json_encode([0 =>"hello", 1=>"haha"]); // JSON.parse chuyển về array
		// echo json_encode(["toán" => 1, "hóa" =>2]); // JSON.parse chuyển về oject
		echo json_encode($cart->convertToArray());
		//json_encode  chuyển array về chuổi dạng json(key:giá trị)
		
	}

	function update() {
		$product_id = $_GET["product_id"];
		$qty = $_GET["qty"];
		$cart = $this->cartStorage->fetch(); // lấy từ trong giỏ hàng ra

		$cart->deleteProduct($product_id); // xóa đi $product_id củ
		$cart->addProduct($product_id, $qty); // cập nhật lại $product_id, $qty

		$this->cartStorage->store($cart); // lưu lại cho lần sau

		echo json_encode($cart->convertToArray()); // lưu xuống trình duyệt
	}

	function delete() {
		$product_id = $_GET["product_id"];
		$cart = $this->cartStorage->fetch();

		$cart->deleteProduct($product_id);

		$this->cartStorage->store($cart);

		echo json_encode($cart->convertToArray()); // đổ dữ liệu về cho trình duyệt
	}
	
}