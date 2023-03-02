<?php 
class PaymentController {
    function checkout() {
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch(); // lấy thông tin trong giỏ hàng ra
        // đk k có sp k cho đặt hàng 
        if(empty($cart->getTotalProductNumber())){
            header("location: index.php?c=product");
            exit;
        }
        $email = "khachvanglai@gmail.com"; // đặt email giả để k login
        if (!empty($_SESSION["email"])) {
            $email = $_SESSION["email"];
        }
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email); // tìm khách hàng dựa vào 2 trường hợp
        // khách vản lai hoặc người mua đã đăng nhập
        require ABSPATH_SITE . "layout/variable_address.php";
        require ABSPATH_SITE . "view/payment/checkout.php";
    }

    function order() {
        $email = "khachvanglai@gmail.com";
        if (!empty($_SESSION["email"])) {
            $email = $_SESSION["email"];
        }
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        $provinceRepository = new ProvinceRepository();
        $province = $provinceRepository->find($_POST["province"]);
        // tạo thông tin lưu xuống data
        $data = [];
        $data["created_date"] = date("Y-m-d H:i:s"); 
        $data["order_status_id"] = 1; // đã đặt hàng
        $data["staff_id"] = null;
        $data["customer_id"] = $customer->getId();
        $data["shipping_fullname"] = $_POST["fullname"];
        $data["shipping_mobile"] = $_POST["mobile"];
        $data["payment_method"] = $_POST["payment_method"];
        $data["shipping_ward_id"] = $_POST["ward"];
        $data["shipping_housenumber_street"] = $_POST["address"];
        $data["shipping_fee"] = $province->getShippingFee();
        $data["delivered_date"] = date("Y-m-d", strtotime("+3 days")); // ngày giao hàng dự kiến, +3 ngày tính từ ngày hôm nay + 3 ngày nữa sẽ giao
        
        $orderRepository = new OrderRepository();
        $orderItemRepository = new OrderItemRepository();
        $order_id = $orderRepository->save($data);
        if (!empty($order_id)) {
            $cartStorage = new CartStorage();
            $cart = $cartStorage->fetch();
            $items = $cart->getItems();
            foreach ($items as $item) {
                // tạo thông tin lưu xuống chi tiết đơn hàng
                $itemData = [];
                $itemData["product_id"] = $item["product_id"]; 
                $itemData["order_id"] = $order_id; 
                $itemData["qty"] =  $item["qty"];
                $itemData["unit_price"] = $item["unit_price"];
                $itemData["total_price"] = $item["total_price"];
                $orderItemRepository->save($itemData);
            }
        }
        $cartStorage->clear(); // làm xong xóa giỏ hàng đi
        $_SESSION["success"] = "Đơn hàng của bạn đã được tạo";
        header("location: index.php?c=product");

    }
    
}
 ?>