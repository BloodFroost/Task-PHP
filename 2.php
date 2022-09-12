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

// Реализовать функцию importXml($a). $a – путь к xml файлу (структура файла приведена ниже). 
// Результат ее выполнения: прочитать файл $a и импортировать его в созданную БД.

function  importXml($a)
{

    $host     = 'localhost';
    $database = 'test_samson';
    $user     = 'root';
    $password = 'root';

    // подключаемся к серверу
    $db = mysqli_connect($host, $user, $password, $database)
        or die("Ошибка " . mysqli_error($db));


    $data = simplexml_load_file($a);

    //Товары
    foreach ($data->Товар as $product) {

        $query = "INSERT INTO a_product VALUES(NULL, $product[Код], \" $product[Название] \")";

        $result = mysqli_query($db, $query) or die("Ошибка " . mysqli_error($db));
        $idProduct = mysqli_insert_id($db);
        if (!$result) throw new Exception('Товар не добавлен в БД');


        //Цены
        foreach ($product->Цена as $price) {

            $query = "INSERT INTO a_price VALUES(NULL, $idProduct, \" $price[Тип] \", \" $price \")";

            $result = mysqli_query($db, $query) or die("Ошибка " . mysqli_error($db));
            if (!$result) throw new Exception('Цены не добавлены в БД');
        }


        //Свойства
        foreach ($product->Свойства->children() as $property) {

            ($property['ЕдИзм'] == NULL) ? $unit = "NULL" : $unit = (string)$property['ЕдИзм'];

            $query = "INSERT INTO a_property VALUES(NULL, $idProduct, \" $property->getName() \", \" $unit\", \"$property\")";

            $result = mysqli_query($db, $query) or die("Ошибка " . mysqli_error($db));
            if (!$result) throw new Exception('Свойства не добавлены в БД');
        }

        //Разделы
        foreach ($product->Разделы->Раздел as $section) {
            $parent_id = 0;
            // Если категории не присвоен код сохраняем в таблице NULL
            ($section['Код'] == NULL) ? $code = "NULL" : $code = (string)$section['Код'];

            //Проверяем есть ли указанныая категория в базе данных
            // если нет добавляем ее в таблицу с категориями
            $query = "SELECT id FROM a_category
             WHERE title like \"$section\"
             AND code " . (($code == "NULL") ? "IS NULL" : ("like" . $code)) . " LIMIT 1";
            $result = mysqli_query($db, $query) or die("Ошибка " . mysqli_error($db));
            if (!$result) throw new Exception('При проверке категории возникла ошибка');
            $idCategory = mysqli_fetch_row($result)[0];

            if ($idCategory == NULL) {

                $query = "INSERT INTO a_category VALUES(NULL, $code , \"$section\", $parent_id)";

                $result = mysqli_query($db, $query) or die("Ошибка " . mysqli_error($db));
                if (!$result) throw new Exception('Категория не добавлены в БД');
                $idCategory = mysqli_insert_id($db);
            }


            //Проверяем есть ли вложенные категории
            if ($section->children()->count() < 0) {

                //добавим товар в категорию
                $query = "INSERT INTO a_product_category VALUES(NULL, $idProduct, $idCategory)";

                $result = mysqli_query($db, $query) or die("Ошибка " . mysqli_error($db));
                if (!$result) throw new Exception('Товар не добавлен в категорию');
            }
        }
    }

    mysqli_close($db);
}
// $a = '2.xml';
// importXml($a);

// Реализовать функцию exportXml($a, $b). 
// $a – путь к xml файлу вида (структура файла приведена ниже), $b – код рубрики.
//  Результат ее выполнения: выбрать из БД товары (и их характеристики, необходимые для формирования файла)
//  выходящие в рубрику $b или в любую из всех вложенных в нее рубрик, сохранить результат в файл $a.

function exportXml($a, $b)
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL ^ E_NOTICE);
    $conn = mysqli_connect("localhost", "root", "root", "test_samson");
    if ($conn->connect_error) {
        die("Ошибка подключения " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT *
                            FROM a_product_category 
                            JOIN a_category ON a_product_category.category_id = a_category.id
                            WHERE a_category.id = ?");    // Выбираю товары по id рубрики

    $stmt->bind_param('s', $b);
    $stmt->execute();


    $dom = new domDocument("1.0", "utf-8");
    $products = $dom->appendChild($dom->createElement('Товары'));


    while ($result = $stmt->get_result()) {
        foreach ($result as $item) {

            $product = $products->appendChild($dom->createElement('Товар'));

            $aProduct = $conn->prepare("SELECT *
                            FROM a_product_category  
                            JOIN a_product ON a_product_category.product_id = a_product.id
                            WHERE a_product.id = ?");

            $aProduct->bind_param('s', $item['product_id']);
            $aProduct->execute();
            while ($rows = $aProduct->get_result()) {
                foreach ($rows as $row)

                    $product->setAttribute('Код', $row['code']);
                $product->setAttribute('Название', $row['title']);
            }
            // Получение Цен
            $stmt = $conn->prepare("SELECT a_price.price_type, a_price.price
                           FROM a_price WHERE a_price.product_id = ?");
            $stmt->bind_param('s', $item['product_id']);
            $stmt->execute();

            // Создание тегов "Цена"
            while ($values = $stmt->get_result()) {
                foreach ($values as $value) {
                    $price = $dom->createElement('Цена', $value['price']);
                    $price->setAttribute('Тип', $value['price_type']);
                    $product->appendChild($price);
                }
            }


            // Получение свойств
            $stmt = $conn->prepare("SELECT *
                                    FROM a_property WHERE a_property.product_id = ?");
            $stmt->bind_param('s', $item['product_id']);
            $stmt->execute();
            $properties = $product->appendChild($dom->createElement('Свойства'));

            // Создание тегов "Свойства"
            while ($values = $stmt->get_result()) {
                foreach ($values as $value) {
                    $propertyName = $value['property'];
                    $propertyValue = $value['value'];
                    $propertyUnit = $value['unit'];

                    if ($propertyUnit === '%') {            // Проверяет наличие единицы измерения в конце строки
                        $propertyValue = $propertyValue . ' ' . $propertyUnit;
                        $property = $dom->createElement($propertyName, $propertyValue);
                        $property->setAttribute('ЕдИзм', $propertyUnit);
                    } else {
                        $property = $dom->createElement($propertyName, $propertyValue);
                    }
                    $properties->appendChild($property);
                }
            }
            // Получение категорий
            $stmt = $conn->prepare("SELECT a_category.title
                                  FROM a_category
                                  JOIN a_product ON a_product.id = a_category.id
                                  WHERE a_product.title = ?");
            $stmt->bind_param('s', $item['title']);
            $stmt->execute();
            $categories = $product->appendChild(($dom->createElement('Разделы')));

            // Создание тегов "Категории"
            while ($values = $stmt->get_result()) {
                foreach ($values as $value) {
                    $category = $dom->createElement('Раздел', $value['title']);
                    $categories->appendChild($category);
                }
            }
        }
    }

    $dom->save($a);
    $conn->close();
}

exportXml('exportXml.xml', 1);
