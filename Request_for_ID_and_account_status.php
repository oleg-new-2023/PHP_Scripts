<?php
# URL Api endpoint для полученич ID по номеру телефона
$url_get_id = (string) 'https://manager.sohonet.ua/api/clients/check-number?number=';
# URL Api endpoint для получения информации по ID
$url_get_status = (string) 'https://manager.sohonet.ua/api/clients/get-profile?account_id=';
#Массив ID полученых по номеру телефона
$ids = array();
#Строка возвращаемая скриптом которая содержит номера ID и баланс
$result_string = "";

#Определяем как передан номер телефона
if ($argv[1] !=0) {
    $user_phone_number = $argv[1];
} elseif ($_GET["phone"] != 0){
    $user_phone_number = $_GET["phone"];
} else {
    echo "no data";
    return;
}
# Добалляем недостающие либо убирау излишние символы, приведя строку к виду : 380XXXXXXXXX
$user_phone_number = trim($user_phone_number);
if (strlen($user_phone_number) > 12) {
    $user_phone_number = substr($user_phone_number, strlen($user_phone_number) - 12);
} elseif (strlen($user_phone_number) == 10) {
    $user_phone_number = "38" . $user_phone_number;
}elseif (strlen($user_phone_number) == 9) {
    $user_phone_number = "380" . $user_phone_number;
}

#Запрос Json строки содержащей ID
$string_response = get_data_from_url($url_get_id, $user_phone_number);
#Проверка наличия телефона в базе "Менеджера" по возвращенной строке
if (strpos($string_response ,'Check number')) {
    echo "no data";
    return;
}
#Преобразование Json строки в массив
$json_response = json_decode($string_response, true);
#Получение массива ID по массиву Json
get_id_Array_from_Json_Array($json_response);
#Проверка массива ID на наличие хотя бы одного значения
if ($ids == null) {
    echo "no data";
    return;
}
#Формирование строки которая содержит номера ID и баланс
foreach ($ids as $key => $value) {
    $string_with_bill = get_data_from_url($url_get_status, $value);
    $json_with_bill = json_decode($string_with_bill, true);
    $balance = get_client_balance($json_with_bill);
    $result_string .= '(id:'. $value . ';'. $balance .'₴)';
}
#Возвращение результата
echo $result_string ;



#Функция возвращающая баланс клиента из Json массива
function get_client_balance($json_array) {
    $balance =round($json_array['data']['bill'], 0);
    return $balance;
}
#Функция возвращающая массив ID из Json массива
function get_id_Array_from_Json_Array($current_array) {
    global $ids;
    foreach($current_array as $key => $value) {
        if (is_array($value)) {
            get_id_Array_from_Json_Array($value);
        }elseif ($key == "id" ) {
            array_push($ids, (int)$value);
        }
    }
}
#Функция возвращающая Json строку по составному URL используя curl
function get_data_from_url($url, $value) {
    $curl_req = curl_init();

    curl_setopt($curl_req, CURLOPT_USERAGENT, filter_input(INPUT_SERVER, 'HTTP_USER_AGENT',
        FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
    curl_setopt($curl_req, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl_req, CURLOPT_HEADER, 0);
    curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_req, CURLOPT_URL, $url . $value);
    curl_setopt($curl_req, CURLOPT_FOLLOWLOCATION, true);

    $data = curl_exec($curl_req);

    curl_close($curl_req);

    return $data;
}
?>
