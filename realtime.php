<?php
include("table2arr.php");
$state = @json_decode(file_get_contents("http://localhost:8098/INFO|".rand(10000,99999)),true);
$entry = @file_get_contents("http://localhost:8098/ENTRY|".rand(10000,99999));
if(empty($json)){
    echo "Closed";
    exit;
}else
    echo "Open<br>";
echo "<b><u>Room Name</u><br>".$state["name"]."<br>";
echo "<u>Track</u><br> ".$state["track"]."<br>";
echo "<u>Online Users</u></b><br>";
    $dom = new DOMe("div");
    $dom->importHTML($entry);
    $rows = $dom->getElementsByTagName("tr");
    $data = array();
    foreach ($rows as $row) {
        $cells = $row->getElementsByTagName("td");
        $cellData = array();
        foreach ($cells as $cell)
            $cellData[] = trim($cell->generate());
        $data[] = $cellData;
    }
    $entry_data = array();
    foreach ($data as $entry1) {
        $entry1 = str_replace(array("<td>","</td>"," ","\r","\n"," "),NULL,$entry1);
        switch($entry1[0]){
            case "ID":
                $table = "entry";
                $count = 0;
                continue;
                break;
            case "POS":
                $table = "pos";
                $count = 0;
                continue;
                break;
            default:
                if($table == "entry" && $entry1[4] == "DC" || $table == "pos" && $entry1[5] == "16666:39:999")
                    continue;
                if($table == "pos"){
                    $count++;
                    $entry1[0] = $count;
                }
                $entry_data[$table][$entry1[1]] = $entry1;
                break;
        }
    }
$drivers = 0;
foreach ($entry_data["entry"] as $clave => $valor) {
    if(!empty($valor["1"]){
        $drivers++;
        echo $valor[1]." - ".$valor[8]."<br>";
    }
}
if($drivers == 0)
    echo "**No users Online**";
else{
    if(isset($entry_data["pos"])){
        echo "<u>Carrera en tiempo real:</u></b><br>";
        echo "<table><tr><td>Posición</td><td>Corredor</td><td>Vueltas</td><td>Tiempo</td><td>Dif</td></tr><tr>";
        foreach ($entry_data["pos"] as $clave => $valor) {
            echo "<tr><td>".$valor[0]."</td><td>".$valor[1]."</td><td>".$valor[4]."</td><td>".$valor[5]."</td><td>".$valor[6]."</td></tr><tr>";
        }
        echo "</table>";
    }
}
?>
