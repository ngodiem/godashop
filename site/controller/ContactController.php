<?php 

class ContactController {
    function form() {
        require ABSPATH_SITE . "view/contact/form.php";
    }
    function send() {
        //     var_dump($_POST);
        // exit;
        $name = $_POST["fullname"];
        $email = $_POST["email"];
        $message = $_POST["content"];
        $moblie = $_POST["mobile"];

        $emailService = new EmailService();
    
        $to = EMAIL_SHOP;
        $subject = "[godashop] khách hàng  $email";
        $content = "khách hàng có email $email liên hệ, <br>
        số điện thoại khách hàng là $moblie, <br>
        nội dung $message";
        if($emailService->send($to, $subject, $content)) {
            echo "đã gởi mail thành công";
        }
        else {
            echo  $emailService->getError();
        }
    }
}
 ?>