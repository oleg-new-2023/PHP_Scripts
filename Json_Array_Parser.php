<?php



$sour_string = '{"status":"success","message":"","listAppRequest":[{"id":6080,"address":"м. Одеса, Космонавтів, ⁶8, кв. 154"}],"users":{"1":{"login":"ID40714","address":" Академика Королева 72а, 1","accounts":[{"id":40714,"address":" Академика Королева 72а, 1"}]},"2":{"login":"ID44835","address":" Академика Королева 92а","accounts":[{"id":44835,"address":" Академика Королева 92а"}]},"3":{"login":"ID49524","address":" Владимира Высоцкого 11","accounts":[{"id":49524,"address":" Владимира Высоцкого 11"}]},"4":{"login":"ID54230","address":" Александра Невского 8","accounts":[{"id":54230,"address":" Александра Невского 8"}]}}}';
$ids = array();

$array_from_json = json_decode($sour_string, true);
var_dump($array_from_json);
get_id_from_Array($array_from_json);
var_dump($ids);
print $ids[2];

function get_id_from_Array($current_attay) {
    global $ids;
foreach($current_attay as $key => $value) {
    if (is_array($value)) {
        get_id_from_Array($value);
    } elseif  ($key == "id") {
        array_push($ids, (int)$value);
    }
}
}
?>
