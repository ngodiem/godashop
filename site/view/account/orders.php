<?php require ABSPATH_SITE . "layout/header.php" ?>
<!-- copy main từ đơn hàng của tôi html -->
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-9">
                <ol class="breadcrumb">
                    <li><a href="/" target="_self">Trang chủ</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span>Tài khoản</span></li>
                </ol>
            </div>
            <div class="clearfix"></div>
            <?php require ABSPATH_SITE . "view/account/mySidebar.php" ?>
            <div class="col-md-9 order">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="home-title">Đơn hàng của tôi</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <!-- Mỗi đơn hàng -->
                        <?php foreach($orders as $order): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Đơn hàng <a href="index.php?c=account&a=orderDetail&id=<?=$order->getId()?>">#<?=$order->getId()?></a></h5>
                                <span class="date">
                                    <?=$order->getCreatedDate()?> </span>
                                <hr>
                                <!--$order->getOrderItems() từ đơn đặt hàng lấy được dòng sp, từ dòng sp lấy ra được sp -->
                                <?php foreach($order->getOrderItems() as $orderItem):
                                $product = $orderItem->getProduct()     ?> 
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="../upload/<?=$product->getFeaturedImage()?>" alt="" class="img-responsive">
                                    </div>
                                    <div class="col-md-3">
                                        <!-- link tên sp-->
                                        <a class="product-name" href="<?=$router->generate("product-detail", ["slugName" => slugify($product->getName()), "id"=>$product->getId()])?>"><?=$product->getName()?></a>
                                    </div>
                                    <div class="col-md-2">
                                        Số lượng: <?=$orderItem->getQty()?>
                                    </div>
                                    <div class="col-md-2">
                                        <!-- từ đơn hàng->lấy ra được trạng thái->lấy ra được mô tả trạng thái -->
                                        <?=$order->getStatus()->getDescription()?>
                                    </div>
                                    <div class="col-md-3">
                                        <?=$order-> getDeliveredDate() ?>
                                    </div>
                                </div>
                                <?php endforeach ?>
                           
                            </div>
                        </div>
                        <?php endforeach ?>
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . "layout/footer.php" ?>