<?php 
// xét địa chỉ dựa vào 2 trường hơp nếu kh k login thì chỉ hiển thị tỉnh, đã login thì hiển thị tất cả theo tỉnh quận xã tương ướng
// tổng cộng 6 thông tin cần lấy ra
//$provinces, $districts, $wards
// $selected_province_id, $selected_district_id, $selected_ward_id
$provinceRepository = new ProvinceRepository();
$provinces = $provinceRepository->getAll(); //1 $provinces  lấy tất cả thông tin tỉnh ra
$districts = []; //quận/huyện
$wards = [];// phường xã
$selected_ward = $customer->getWard();//  trả selected_ward(phường/xã đang ở)
$selected_province_id = null;
$selected_district_id = null;
$selected_ward_id = null;
$shipping_fee = 0;
if (!empty($selected_ward)) {
    $selected_ward_id = $selected_ward->getId(); // 2 $selected_ward_id
    $selected_district = $selected_ward->getDistrict(); // từ $selected_ward -> $selected_district
    $selected_district_id = $selected_district->getId(); // 3 $selected_district_id
    $selected_province = $selected_district->getProvince(); // từ  $selected_district_id-> $selected_province
    $selected_province_id = $selected_province->getId(); // 4 $selected_province_id
    $districts = $selected_province->getDistricts(); // 5 $districts
    $wards =  $selected_district->getWards(); // 6  $wards
    $shipping_fee = $selected_province->getShippingFee(); // tỉnh được chọn-> phí giao hàng
}
 ?>