<?php
function findSimple( $a, $b)
{
$arr=range($a,$b);
$primeArr = $arr;
    foreach ($primeArr as $key => $value) {
        if ($value == 1) {
            unset($primeArr[$key]);
            continue;
        }

        for ($i = 2; $i <= floor($value / $i); $i++) {
                if ($value % $i == 0) {
                unset($primeArr[$key]);
                break;
            }
        }
    }

    return array_values($primeArr);
}
 print_r (findSimple(1,100));
function createTrapeze($a){
$chunkArray=array_chunk($a,3,false);
$newArray=array();
$count=0;
    if(count ($a)%3!==0) {
        throw new lengthException("Введите массив кратный 3\n");
    }
foreach ($chunkArray as $row){

        $arr1=['a','b','c'];
        $newArray[$count]= array_combine($arr1,$row);
        $count++;


}
return $newArray;

}

//print_r(createTrapeze([1,2,3,4,5,6,7,8,9]));
function squareTrapeze($a){
for($i=0;$i<count($a);$i++){
$arr=$a[$i];
$a[$i]['s']=((($arr['a']+$arr['b'])/2)*$arr['c']);
}
return $a;
}
$trapeze = createTrapeze([1, 2, 3, 4, 5, 6, 23, 40, 34]);
squareTrapeze($trapeze);


function getSizeForLimit($a, $b){
$newArr=array();
for($i=0;$i<count($a);$i++){
    $arr=$a[$i];
    if($arr['s']<=$b){
        array_push($newArr,$arr);
    }
}
if(count($newArr)==0){
    throw new lengthException ('Значение площади трапеции не соответсвует максимальному допустимомоу значению!');
    return ;
}
$maxArr=$newArr[0];
foreach ($newArr as $row){
    if($row['s']>$maxArr['s']){
        $maxArr=$row;
    }
}
return $maxArr;
}
$b=1;

//print_r(getSizeForLimit(squareTrapeze($trapeze),$b));
function getMin($a){
    $min=PHP_INT_MAX;
    foreach ($a as $row){
        if($row<$min){
            $min=$row;
        }
    }
    return $min;
}
//print_r(getMin([10,8,9,4,2]));
function  printTrapeze($a){

    echo '<table cellpadding="5" cellspacing="0" border="1">'; //отрисовка таблицы
    echo '<tr> <td>Длина</td> <td>Ширина</td> <td>Высота</td> <td>Площадь</td>'; //шапка таблицы
    foreach ($a as $key => $value) {

        echo "<tr>";
        foreach ($value as $key => $data)
            if($data % 2 !==0 && $key == 's'){// определение нечетных площадей трапеции в таблице
                echo "<td style='color:red;'>$data</td>" ;

            }else {

                echo "<td>".$data."</td>";
            }
        echo "</tr>";
    }

    echo "</table>";
}
$sTrapeze = squareTrapeze($trapeze);
//print_r(printTrapeze($sTrapeze);

abstract class BaseMath{
public function exp1($a, $b, $c){
    $formula=$a*($b**$c);
    return $formula;
}
public function exp2($a, $b, $c){
$formula2=($a/$b)**$c;
return $formula2;
}
abstract public function getValue();


}

class  F1 extends BaseMath{


    public int $a;
    public int $b;
    public int $c;

    public function __construct(int $a, int $b, int $c)
{
    $this->a=$a;
    $this->b=$b;
    $this->c=$c;
}

    public function getValue()
    {
       $exp1=$this->exp1($this->a,$this->b,$this->c);
       $f=($exp1)+((($this->a/$this->c)**$this->b)%3)**min($this->a,$this->b,$this->c);
       return $f;
    }
}
$a = 15;
$b = 10;
$c = 11;

$count =  new F1($a,$b,$c);
//print_r($count->getValue());
