<?php 
// echo __DIR__; //C:\xampp\htdocs\backend\godashop
session_start();
require_once "config.php";
require_once ABSPATH . "bootstrap.php";
require_once ABSPATH_SITE . "load.php";
$router = new AltoRouter();

// map trang chủ
// gỏ godashop.com/ -> trang chủ
// / là đường dẫn tính từ domain
$router->map( 'GET', '/', ["HomeController", "list"], "home"); // gọi hàm list trong file HomeController đặt $router là home

// trang sản phẩm
$router->map( 'GET', '/san-pham', array("ProductController", "list"), 'product');

//trang chính sách đổi trả
$router->map( 'GET', '/chinh-sach-doi-tra', array("InformationController", "returnPolicy"), 'returnPolicy'); // gặp đường link '/chinh-sach-doi-tra' chạy hàm returnPolicy

// trang chính sách thanh toán
$router->map( 'GET', '/chinh-sach-thanh-toan', array("InformationController", "paymentPolicy"), 'paymentPolicy');

// trang chính sách giao hàng
$router->map( 'GET', '/chinh-sach-giao-hang', array("InformationController", "deliveryPolicy"), 'deliveryPolicy');

// trang liên hệ
$router->map( 'GET', '/lien-he', array("ContactController", "form"), 'contact-form');


// trang chi tiết sản phẩm
// dùng hàm call_user_func_array để lấy $_GET ra
// không được dùng slug-name do không hiểu dấu - trong tên
// sinh ra đường dẫn ngược
$router->map('GET', '/san-pham/[*:slugName]-[i:id].html', function($slugName, $id) {
	$_GET["id"] = $id;
  	call_user_func_array(["ProductController", "detail"],[]);
}, 'product-detail');

// trang danh mục
// không đựơc dùng slug-name do không được đặt tên biến có dấu -
$router->map('GET', '/danh-muc/[*:slugName]-[i:categoryId]', function($slugName, $categoryId) {
	$_GET["category_id"] = $categoryId;
  	call_user_func_array(["ProductController", "list"],[]);
}, 'category');

// khoảng giá
$router->map('GET', '/khoang-gia/[*:priceRange]', function($priceRange) {
	$_GET["price-range"] = $priceRange;
  	call_user_func_array(["ProductController", "list"],[]);
}, 'price-range');

// Tìm kiếm
$router->map('GET', '/search', function() {
    call_user_func_array(["ProductController", "list"],[]);
}, 'search');

// match current request url
$match = $router->match();
$routeName = !empty($match["name"]) ? $match["name"] : null ;// dùng để active

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// không gọi được $router home của "HomeController"
  
    $c = !empty($_GET["c"]) ? $_GET["c"]: "home";
    $a = !empty($_GET["a"]) ? $_GET["a"]: "list";
    
  
    
    
    $controller = ucfirst($c) . "Controller";//HomeController
   
    $controller = new $controller();//new HomeController()
    $controller->$a();
}

function slugify($str)
{
  	$str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}
?>