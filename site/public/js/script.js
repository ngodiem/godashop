function openMenuMobile() {
    $(".menu-mb").width("250px");
    $(".btn-menu-mb").hide("slow");
}

function closeMenuMobile() {
    $(".menu-mb").width(0);
    $(".btn-menu-mb").show("slow");
}

$(function(){

        // Thay đổi province
        $("main .province").change(function(event) { // change là thây đổi tỉnh
            /* Act on the event */
            var province_id = $(this).val();
            if (!province_id) { // k chọn tỉnh nào hết
                updateSelectBox(null, "main .district"); // update data=null, cập nhật vào .district
                updateSelectBox(null, "main .ward");
                return; // kết thúc luôn k chạy code phía dưới
            }
    
            $.ajax({ // ngược lại nếu có chọn thì gửi rq lên sv
                url: '/index.php?c=address&a=getDistricts',
                type: 'GET',
                data: {province_id: province_id}
            })
            .done(function(data) { // lấy quận/ huyện về, cập nhật .district
                updateSelectBox(data, "main .district");
                updateSelectBox(null, "main .ward"); //.ward null
            });
    
            if ($("main .shipping-fee").length) { // kt xem có class .shipping-fee k?
                $.ajax({ // nếu có gửi rq ajax lên 
                    url: '/index.php?c=address&a=getShippingFee', // yêu cầu chạy hàm getShippingFee
                    type: 'GET',
                    data: {province_id: province_id} // đủa mã tỉnh lên
                })
                .done(function(data) { // dữ liệu sv đổ về cập nhật vào
                    //update shipping fee and total on UI
                    let shipping_fee = Number(data);
                    let payment_total = Number($("main .payment-total").attr("data")) + shipping_fee; // cập nhật xong cộng lại
    
                    $("main .shipping-fee").html(number_format(shipping_fee) + "₫"); // cập nhật vào .shipping-fee
                    $("main .payment-total").html(number_format(payment_total) + "₫");
                });
            }
    
            
        });

        
    // Thay đổi district
    $("main .district").change(function(event) { // change thay đổi tỉnh
        /* Act on the event */
        var district_id = $(this).val();
        if (!district_id) { // nếu người dùng không chọn mã tỉnh 
            updateSelectBox(null, "main .ward"); //resest .ward= null
            return;
        }

        $.ajax({
            url: '/index.php?c=address&a=getWards',
            type: 'GET',
            data: {district_id: district_id} // gửi district_id yêu cầu chạy hàm getWards
        })
        .done(function(data) {
            updateSelectBox(data, "main .ward"); // lấy .ward về updateSelectBox
        });
    });
        // Thêm sản phẩm vào giỏ hàng
        $("main .buy-in-detail").click(function(event) {
            /* Act on the event */
            var qty = $(this).prev("input").val();//prev là anh trước nó
            var product_id = $(this).attr("product-id");
            $.ajax({
                url: '/index.php?c=cart&a=add',
                type: 'GET',
                data: {product_id: product_id, qty: qty}
            })
            .done(function(data) {
                displayCart(data);
                
            });
        });
        //Hiển thị cart khi load xong trang web
        $.ajax({
            url: '/index.php?c=cart&a=display',
            type: 'GET'
        })
        .done(function(data) {// trả dữ liệu về bỏ trong biến data
            displayCart(data); // chuyển dữ liệu về hàm displayCart
            
        });
        
     // Thêm sản phẩm vào giỏ hàng
     $("main .buy").click(function(event) {
        /* Act on the event */
        
        var product_id = $(this).attr("product-id"); // khi click vào thêm vào giỏ hàng lấy giá trị product-id = 2 đây lên server yêu cầu chạy hàm ajax yêu cầu sv trả về kq(thêm vào giỏ hàng)
        $.ajax({
            url: '/index.php?c=cart&a=add',
            type: 'GET',
            data: {product_id: product_id, qty:1}
        })
        .done(function(data) {
            // console.log(data);
            displayCart(data);
        });
    });

    $("form.register").submit(function(event) {
        /* Act on the event */
        event.preventDefault();
        var email = $(this).find("input[type=email]").val();
        var emailTag  = $(this).find("input[type=email]");
        var formTag = this;
        $.ajax({
            url: '/index.php?c=customer&a=existing',
            data: {email: email},
        })
        .done(function(data) {
            var data = JSON.parse(data);
            if (data.existing == 1) {
                $(emailTag).next().html("Email đã tồn tại");
                $(emailTag).css("border", "1px solid red");
            }
            else {
                formTag.submit();
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    });

    
      // Submit form liên hệ
      $("form.form-contact").submit(function(event) {
        /* Act on the event */
        event.preventDefault(); //ngăn chặn phần tử đó k cho submit để ajax
        var post_url = $(this).attr("action"); //get form action url
        var request_method = $(this).attr("method"); //get form GET/POST method
        var form_data = $(this).serialize(); //Encode form elements for submission tự động nói đường link 
        // tạo các param và nói các param lại với nhau để đẩy lên server
        // alert(form_data)
        $(".message").html("Hệ thống đang gởi email... Vui lòng chờ");
        $("button[type=submit]").attr("disabled", "disabled"); // active nút submit
        $.ajax({
            url: post_url,
            type: request_method,
            data: form_data
        })
        .done(function(data) { // biến data chứa dữ liệu trên server đổ về
            // alert(data);
            $(".message").html(data);
            $("button[type=submit]").removeAttr("disabled"); // cho phép nút submit đó active lại
        });
    });

    // Tìm kiếm và sắp xếp sản phẩm
    $('#sort-select').change(function(event) {
        // var str_param = getUpdatedParam("sort", $(this).val());
        // window.location.href = "/index.php?" + str_param;
        var str_param = getUpdatedParam("sort", $(this).val());
        window.location.href = $(this).attr("url") + "?" + str_param;
    });

    // Ajax search
    var timeout = null;
    $("header form.header-form .search").keyup(function(event) { // 
        /* Act on the event */
        clearTimeout(timeout); // xóa Timeout không cho chạy cái trước đó 
        var pattern = $(this).val(); // lấy dữ liệu người dùng gõ vào
        $(".search-result").html(""); // dữ liệu trên server đổ về k có gì
        timeout = setTimeout(function(){ // hết thời gian đó nó mới chạy
            if (pattern) {
                $.ajax({
                    url: '/index.php?c=product&a=ajaxSearch', // chạy hàm ajaxSearch
                    type: 'GET',
                    data: {pattern: pattern}, // gủi mẫu về
                })
                .done(function(data) {
                    $(".search-result").html(data); // cập nhật cho thẻ .
                    $(".search-result").show(); // hiển thị ra
                    
                });
            }
            
        },500); // sau 5s gửi 1 request lên server hiển thị sp ra
        
    });
    

    // Tìm kiếm theo range
    $('main .price-range input').click(function(event) {
        /* Act on the event */
        // var price_range = $(this).val();
        // window.location.href="index.php?c=product&price-range=" + price_range;
        var url = $(this).attr("url"); // lấy gía trị của url
        window.location.href = url; // truyền url vào
    });

    $(".product-container").hover(function(){
        $(this).children(".button-product-action").toggle(400);
    });

    // Display or hidden button back to top
    $(window).scroll(function() { 
		 if ($(this).scrollTop()) { 
			 $(".back-to-top").fadeIn();
		 } 
		 else { 
			 $(".back-to-top").fadeOut(); 
		 } 
	 }); 

    // Khi click vào button back to top, sẽ cuộn lên đầu trang web trong vòng 0.8s
	 $(".back-to-top").click(function() { 
		$("html").animate({scrollTop: 0}, 800); 
	 });

	 // Hiển thị form đăng ký
	 $('.btn-register').click(function () {
	 	$('#modal-login').modal('hide');
        $('#modal-register').modal('show');
    });

	 // Hiển thị form forgot password
	$('.btn-forgot-password').click(function () {
		$('#modal-login').modal('hide');
    	$('#modal-forgot-password').modal('show');
    });

	 // Hiển thị form đăng nhập
	$('.btn-login').click(function () {
    	$('#modal-login').modal('show');
    });

	// Fix add padding-right 17px to body after close modal
	// Don't rememeber also attach with fix css
	$('.modal').on('hide.bs.modal', function (e) {
        e.stopPropagation();
        $("body").css("padding-right", 0);
        
    });

    // Hiển thị cart dialog
    $('.btn-cart-detail').click(function () {
    	$('#modal-cart-detail').modal('show');
    });

    // Hiển thị aside menu mobile
    $('.btn-aside-mobile').click(function () {
        $("main aside .inner-aside").toggle();
    });

    // Hiển thị carousel for product thumnail
    $('main .product-detail .product-detail-carousel-slider .owl-carousel').owlCarousel({
        margin: 10,
        nav: true
        
    });
    // Bị lỗi hover ở bộ lọc (mobile) & tạo thanh cuộn ngang
    // Khởi tạo zoom khi di chuyển chuột lên hình ở trang chi tiết
    // $('main .product-detail .main-image-thumbnail').ezPlus({
    //     zoomType: 'inner',
    //     cursor: 'crosshair',
    //     responsive: true
    // });
    
    // Cập nhật hình chính khi click vào thumbnail hình ở slider
    $('main .product-detail .product-detail-carousel-slider img').click(function(event) {
        /* Act on the event */
        $('main .product-detail .main-image-thumbnail').attr("src", $(this).attr("src"));
        var image_path = $('main .product-detail .main-image-thumbnail').attr("src");
        $(".zoomWindow").css("background-image", "url('" + image_path + "')");
        
    });

    $('main .product-detail .product-description .rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'md',
        stars: "5",
        showClear: false,
        showCaption: false
    });

    $('main .product-detail .product-description .answered-rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'md',
        stars: "5",
        showClear: false,
        showCaption: false,
        displayOnly: false,
        hoverEnabled: true
    });

    $('main .ship-checkout[name=payment_method]').click(function(event) {
        /* Act on the event */
    });

    $('input[name=checkout]').click(function(event) {
        /* Act on the event */
        window.location.href="/index.php?c=payment&a=checkout";
    });

    $('input[name=back-shopping]').click(function(event) {
        /* Act on the event */
        window.location.href="/index.php?c=product";
    });
    
    // Hiển thị carousel for relative products
    $('main .product-detail .product-related .owl-carousel').owlCarousel({
        loop:false,
        margin: 10,
        nav: true,
        dots:false,
        responsive:{
        0:{
            items:2
        },
        600:{
            items:4
        },
        1000:{
            items:5
        }
    }
        
    });
});

// Login in google
function onSignIn(googleUser) {
    var id_token = googleUser.getAuthResponse().id_token;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://study.com/register/google/backend/process.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      console.log('Signed in as: ' + xhr.responseText);
    };
    xhr.send('idtoken=' + id_token);
}

// Cập nhật giá trị của 1 param cụ thể
function getUpdatedParam(k, v) {//sort, price-asc
    var params={};
    //params = {"c":"proudct", "category_id":"5", "sort": "price-desc"}
    // window.location.search = "c=product&price-range=200000-300000&sort=price-desc"
    window.location.search
      .replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str,key,value) {
        params[key] = value;
        // alert(str);
        //[?&] là param
        // chạy 3 lần 


      }
    );
 // mục tiêu là cập nhật giá trị v dựa vào k    
    //{"c":"proudct", "category_id":"5", "sort": "price-desc"}
    params[k] = v;
    if (v == "") {
        delete params[k];
    }
// câp nhật oj xong chuyển về chuổi
    var x = [];//là array
    for (p in params) {
        //x[0] = 'c=product'
        //x[1] = 'category_id=5'
        //x[2] = 'sort=price-asc'
        x.push(p + "=" + params[p]); // push là thêm vào cuối array
        // p là tên thuộc tính params[p] là giá trị thuộc tính đó
    }
    return str_param = x.join("&");//c=product&category_id=5&sort=price-asc
    // dùng join để tạo ra đường dẫn
}


// Paging
function goToPage(self, page) {
    var str_param = getUpdatedParam("page", page); // update thuộc tính "page" nếu k có thì thêm vào, gọi page bên list truyền vào
    window.location.href = $(self).parents("ul").attr("url") + "?" + str_param;
}
// Hiển thị cart(cập nhật)
//href="' + item.url +' "
function displayCart(data) {
    var cart = JSON.parse(data); // hàm JSON.parse chuyển chuổi có dạng json sang array hoặc oject
    
    var total_product_number = cart.total_product_number;
    $(".btn-cart-detail .number-total-product").html(total_product_number);

    var total_price = cart.total_price;
    $("#modal-cart-detail .price-total").html(number_format(total_price)+"₫");
    var items = cart.items;
    var rows = "";
    for (let i in items) {
        let item = items[i];
        var row = 
                '<hr>'+
                '<div class="clearfix text-left">'+   
                    '<div class="row">'+             
                        '<div class="col-sm-6 col-md-1">'+
                            '<div>'+
                                '<img class="img-responsive" src="../upload/' + item.img + '" alt="' + item.name + ' ">'+             
                            '</div>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<a class="product-name" href="' + item.url +' ">' + item.name + '</a>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-2">'+
                            '<span class="product-item-discount">' + number_format(Math.round(item.unit_price)) + '₫</span>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-3">'+
                            '<input type="hidden" value="1">'+
                            '<input type="number" onchange="updateProductInCart(this,'+ item.product_id +')" min="1" value="' + item.qty + '">'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-2">'+
                            '<span>' + number_format(Math.round(item.total_price)) + '₫</span>'+
                        '</div>'+
                        '<div class="col-sm-6 col-md-1">'+
                            '<a class="remove-product" href="javascript:void(0)" onclick="deleteProductInCart('+ item.product_id +')">'+
                                '<span class="glyphicon glyphicon-trash"></span>'+
                            '</a>'+
                        '</div>'+ 
                    '</div>'+                                                   
                '</div>';
        rows += row; 
    }
    $("#modal-cart-detail .cart-product").html(rows);
}
// Thay đổi số lượng sản phẩm trong giỏ hàng
function updateProductInCart(self, product_id) {
    var qty = $(self).val(); // lấy giá trị đẩy lên
    $.ajax({
        url: '/index.php?c=cart&a=update', // / là tính từ đường dẫn gốc
        type: 'GET',
        data: {product_id: product_id, qty: qty}
    })
    .done(function(data) {
        displayCart(data);
        
    });
}

function deleteProductInCart(product_id) {
    $.ajax({
        url: '/index.php?c=cart&a=delete',
        type: 'GET',
        data: {product_id: product_id}
    })
    .done(function(data) {
        displayCart(data); // cập nhật 3 thông tin total_product_number, total_price
        
    });
}

    // Cập nhật các option cho thẻ select
function updateSelectBox(data, selector) {
    var items = JSON.parse(data); // chuyển data sang array
    $(selector).find('option').not(':first').remove(); // tìm option loại trừ đi cái đầu tiên còn lại xóa đi
    if (!data) return;
    for (let i = 0; i < items.length; i++) {
        let item = items[i]; // biến let chỉ sống trong khói for bên ngoài k truy cập được let(giới hạn), biến var(không giới hạn) thì đâu củng truy cật được
        let option = '<option value="' + item.id + '"> ' + item.name + '</option>'; // tạo op để ép vào
        $(selector).append(option); // append thêm vào
    } 

    
}
