<?php 
// các giá trị được xem là empty: 0,  false, chỉ số không tồn tại, " ", null, không tồn tại biến
// giá trị rổng: 0, null, false, " ";
$x = 0;
if(empty($x)) {
    echo " x empty"; // x là empty
}
echo "br";
$z = [3,5];
if(empty($z[2])) {
    echo "phần tử có chỉ số 2 không tồn tại";
}
// isset là tồn tại, có hay không, vd: biến tồn tại, hoặc chỉ số tồn tại

// not isset là tập hợp con của empty
if(isset($a)) {
    echo "a có tồn tại";
}
else {
    echo "a không tồn tại"; // a không tồn tại
}
 ?>