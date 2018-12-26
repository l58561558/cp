<?php
namespace app\home\model;
use think\Model;
// NBA比赛竞猜
class Group extends Model
{   
    /*
    *$data     array  投注选项数据
    */
    public function tz_num($data)
    {
        $chuan = explode(',', $data['chuan']);
        $touz = $data['tz'];

        $dan  = array();
        $tz  = array();
        $tz_num = 0;
        // 根据串法将含有胆的比赛 和 没有胆的比赛 分开组合 在进行组合
        foreach ($touz as $key => $value) {
            if($touz[$key]['dan'] == 1){
                $dan[] = count($touz[$key]['tz_result']);
            }else{
                $tz[] = count($touz[$key]['tz_result']);
            }
        }
        $dan_count = count($dan);
        foreach ($chuan as $key => $value) {
            if(!empty($dan)){
                $dan_res = $this->combination($dan,count($dan))[0];
                $tz_res = $this->combination($tz,$chuan[$key]-$dan_count);
                if(count($dan_res) > 1){
                    $dan_res = array_product($dan_res);
                }else{
                    $dan_res = $dan_res[0];
                }
                foreach ($tz_res as $ke => $val) {
                    $tz_res[$ke][] = $dan_res;
                    $tz_num += array_product($tz_res[$ke]);
                }
            }else{
                if($chuan[$key] == count($tz)){
                    $tz_num += array_product($tz);
                }else{
                    $res = $this->combination($tz,$chuan[$key]);
                    foreach ($res as $val) {
                        $tz_num += array_product($val);
                    }
                }
            }
        }
        return $tz_num*$data['multiple'];
    }

    // 组合
    public function combination($a, $m) {
        $r = array();
        $n = count($a);
        if ($m <= 0 || $m > $n) {
            return $r;
        }
        for ($i=0; $i<$n; $i++) {
            $t = array($a[$i]);
            if ($m == 1) {
                $r[] = $t;
            } else {
                $b = array_slice($a, $i+1);
                $c = $this->combination($b, $m-1);
                foreach ($c as $k => $v) {
                    $r[] = array_merge($t, $v);
                }   
            }
        }
        return $r;
    }



    public function get_group($chuan, $data)
    {
        foreach ($data as $key => $value) {
            if($data[$key]['dan'] == 1){
                if(is_string($data[$key]['tz_result'])){
                    $dan_data[$key] = explode(',', $data[$key]['tz_result']);
                }else{
                    $dan_data[$key] = $data[$key]['tz_result'];
                }
                
            }else{
                if(is_string($data[$key]['tz_result'])){
                    $tz_data[$key] = explode(',', $data[$key]['tz_result']);
                }else{
                    $tz_data[$key] = $data[$key]['tz_result'];
                }
            }
        }
        if(!empty($dan_data)){
            $dan_data = array_merge($dan_data);
            $tz_data = array_merge($tz_data);

            $dan_count = count($dan_data);
            $dan_order_group = $this->order_group($dan_count,$dan_data);
            $tz_order_group = $this->order_group($chuan-$dan_count,$tz_data);
            $group_data[] = $dan_order_group;
            $group_data[] = $tz_order_group;
            $group_data = $this->order_group(2,$group_data);   
        }else{
            $group_data = $this->order_group($chuan,$tz_data);
        }
        
        return $group_data;
    }

    /*
    *$chuan    int    串法
    *$touz     array  投注选项数据
    */
    public function order_group($chuan,$touz)
    {
        $order_group = array();
        for ($i=0; $i < count($touz); $i++) { 
            for ($j=0; $j < count($touz[$i]); $j++) { 
                if($chuan == 1){
                    $order_group[] = $touz[$i][$j];
                }else{
                    for ($a=1; $a < count($touz); $a++) { 
                        if(isset($touz[$i+$a])){
                            for ($b=0; $b < count($touz[$i+$a]); $b++) { 
                                if($chuan == 2){
                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] ]);
                                }else{
                                    for ($c=1; $c < count($touz); $c++) { 
                                        if(isset($touz[$i+$a+$c])){
                                            for ($d=0; $d < count($touz[$i+$a+$c]); $d++) { 
                                                if($chuan == 3){
                                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] , $touz[$i+$a+$c][$d]]);
                                                }else{
                                                    for ($e=1; $e < count($touz); $e++) { 
                                                        if(isset($touz[$i+$a+$c+$e])){
                                                            for ($f=0; $f < count($touz[$i+$a+$c+$e]); $f++) { 
                                                                if($chuan == 4){
                                                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] , $touz[$i+$a+$c][$d] , $touz[$i+$a+$c+$e][$f]]);
                                                                }else{
                                                                    for ($g=1; $g < count($touz); $g++) { 
                                                                        if(isset($touz[$i+$a+$c+$e+$g])){
                                                                            for ($h=0; $h < count($touz[$i+$a+$c+$e+$g]); $h++) { 
                                                                                if($chuan == 5){
                                                                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] , $touz[$i+$a+$c][$d] , $touz[$i+$a+$c+$e][$f] , $touz[$i+$a+$c+$e+$g][$h]]);
                                                                                }else{
                                                                                    for ($k=1; $k < count($touz); $k++) { 
                                                                                        if(isset($touz[$i+$a+$c+$e+$g+$k])){
                                                                                            for ($l=0; $l < count($touz[$i+$a+$c+$e+$g+$k]); $l++) { 
                                                                                                if($chuan == 6){
                                                                                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] , $touz[$i+$a+$c][$d] , $touz[$i+$a+$c+$e][$f] , $touz[$i+$a+$c+$e+$g][$h] , $touz[$i+$a+$c+$e+$g+$k][$l]]);
                                                                                                }else{
                                                                                                    for ($m=1; $m < count($touz); $m++) { 
                                                                                                        if(isset($touz[$i+$a+$c+$e+$g+$k+$m])){
                                                                                                            for ($n=0; $n < count($touz[$i+$a+$c+$e+$g+$k+$m]); $n++) { 
                                                                                                                if($chuan == 7){
                                                                                                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] , $touz[$i+$a+$c][$d] , $touz[$i+$a+$c+$e][$f] , $touz[$i+$a+$c+$e+$g][$h] , $touz[$i+$a+$c+$e+$g+$k][$l] , $touz[$i+$a+$c+$e+$g+$k+$m][$n]]);
                                                                                                                }else{
                                                                                                                    for ($o=1; $o < count($touz); $o++) { 
                                                                                                                        if(isset($touz[$i+$a+$c+$e+$g+$k+$m+$o])){
                                                                                                                            for ($p=0; $p < count($touz[$i+$a+$c+$e+$g+$k+$m+$o]); $p++) {
                                                                                                                                if($chuan == 8){ 
                                                                                                                                    $order_group[] = implode(',', [ $touz[$i][$j] , $touz[$i+$a][$b] , $touz[$i+$a+$c][$d] , $touz[$i+$a+$c+$e][$f] , $touz[$i+$a+$c+$e+$g][$h] , $touz[$i+$a+$c+$e+$g+$k][$l] , $touz[$i+$a+$c+$e+$g+$k+$m][$n] , $touz[$i+$a+$c+$e+$g+$k+$m+$o][$p]]);
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }    
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } 
                                }
                            }
                        }
                    }    
                }
            }
        }
        return $order_group;
    }
}
