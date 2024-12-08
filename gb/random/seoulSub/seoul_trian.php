<?php
include('../../common.php'); 
include('../key/seoul_key.php');

$count = 108;
$seoul_api_url = "http://swopenAPI.seoul.go.kr/api/subway/" . $seoul_key . "/json/realtimePosition/0/" . $count . "/1호선"; //열차
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $seoul_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$re = curl_exec($ch);
curl_close($ch);
$data = json_decode($re, true);

$seoul_station_url = "http://openapi.seoul.go.kr:8088/" . $seoul_key . "/json/SearchSTNBySubwayLineInfo/1/" . $count . "///1호선"; // 지하철역 이름
$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, $seoul_station_url);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
$re1 = curl_exec($ch1);
curl_close($ch1);
$data1 = json_decode($re1, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subway Tracker</title>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
}

.tracker {
    position: relative;
    width: 100%;
    height: 150px;
    overflow-x: scroll;
    white-space: nowrap;
    background: #fff;
    padding: 10px 0;
}

.line {
    position: relative;
    display: flex;
    align-items: center;
    height: 100px;
    background: #ddd;
    border-radius: 5px;
}

.station {
    position: relative;
    width: 50px;
    height: 50px;
    background: #007bff;
    border-radius: 50%;
    text-align: center;
    line-height: 50px;
    color: white;
    margin: 0 15px;
}

.train {
    position: absolute;
    width: 30px;
    height: 30px;
    background: #ff5733;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

</style>
<body>
    <div class="tracker">
        <div class="line">
<?php

if(isset($data1['SearchSTNBySubwayLineInfo']['row'])){
    foreach($data1['SearchSTNBySubwayLineInfo']['row'] as $index1 => $train){ 
?>
        <div class="station" id="station<?=$index1?>"> <?=$train['STATION_NM'] ?></div>
<?php
    } 
}
?>

        </div>        

        <?php
if(isset($data['realtimePositionList'])){
    foreach($data['realtimePositionList'] as $index => $train1){ 
?>        
        <!-- 열차 -->
        <div class="train" id="train<?=$index?>"><?=$train1['trainNo'] ?></div>
<?php
    }   
}
?>
    </div>
</body>
<script>
document.addEventListener("DOMContentLoaded", () => {
    let s_count = "<?=$count?>";
    // 열차 데이터 (API에서 가져온 데이터로 대체)
    const trains = [];
    for (let i=0; i<=s_count; i++){
        trains.push (  { id: `train${i}`, currentStation:`${i}` });
    }

    // 역 DOM 요소들
    const stations = document.querySelectorAll(".station");

    // 열차 위치 업데이트
    trains.forEach(train => {
        const trainElement = document.getElementById(train.id);
        const currentStation = stations[train.currentStation - 1]; // 현재 역 ID 기반으로 찾기

        if (currentStation) {
            if(!currentStation.offsetLeft){
                trainElement.style.left = `${currentStation.offsetLeft}px`;
            }

        }
    });

    // 주기적으로 업데이트 (API에서 데이터 가져오는 부분과 연결)
    setInterval(() => {
        // API에서 새 데이터 가져오기 (여기서는 임의로 데이터 변경)
        for (let i=0; i<=s_count; i++){
            trains[i].currentStation = (trains[i].currentStation % s_count) + 1; // 다음 역으로 이동
        }

        // 열차 위치 다시 설정
        trains.forEach(train => {
            const trainElement = document.getElementById(train.id);
            const currentStation = stations[train.currentStation - 1];
            
            if (currentStation) {
                trainElement.style.left = `${currentStation.offsetLeft}px`;
            }
        });
    }, 5000); // 5초마다 업데이트
});
</script>    
