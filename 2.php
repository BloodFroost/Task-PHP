<?php
// convertString($a, $b). Результат ее выполнение: если в строке $a содержится 2 и более подстроки $b,
// то во втором месте заменить подстроку $b на инвертированную подстроку.

function convertString($a, $b)
{
    $substringСheck = (mb_substr_count($a, $b));
    if ($substringСheck >= 2) {
        $pos = stripos($a, $b, 2);
        $b2 = strrev($b);
        $b2Length = mb_strlen($b2);
        $pos1 = substr_replace($a, $b2, $pos, $b2Length);
        return $pos1;
    } else {
        return $a;
    }
}
// $a = "string substring string substring string substring";
// $b = "substring";
//print_r(convertString($a, $b));

// c. $a – двумерный массив вида [['a'=>2,'b'=>1],['a'=>1,'b'=>3]], 
// $b – ключ вложенного массива.
// Результат ее выполнения: двумерном массива $a отсортированный по возрастанию значений для ключа $b. 
// В случае отсутствия ключа $b в одном из вложенных массивов, выбросить ошибку класса Exception с индексом неправильного массива.


function mySortForKey($a, $b)
{
    foreach ($a as $key => $value) {
        if ($value[$b] == null) {
            throw new Exception('Индекс масссива неверный: ' . $a[$value][$key]);
        } else {
            $keys = array_column($a, $b);
            $sort = array_multisort($keys, SORT_ASC, $a);
            return $a;
        }
    }
}
$a = [
    ['a' => 2, 'b' => 3],
    ['a' => 3, 'b' => 5],
    ['a' => 1, 'b' => 2]
];
$b='b';
print_r(mySortForKey($a,$b));