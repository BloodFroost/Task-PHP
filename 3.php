<?php
namespace Test3;

//use Exception;   //Нужно подключить класс

class newBase
{
//    static private int $count = 0;
    static private $count = 0;
    static private $arSetName = [];
    /**
     * @param string $name
     */
   function __construct(int $name = 0)
//    function __construct(string $name)
    {
        if (empty($name)) {
            while (array_search(self::$count, self::$arSetName) != false) {
                ++self::$count;
            }
            $name = self::$count;
        }
        $this->name = $name;
        self::$arSetName[] = $this->name;
    }
    private $name;
//  protected $name;
    /**
     * @return string
     */
    public function getName(): string
    {
        return '*' . $this->name  . '*';
    }
    protected $value;
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getSize()
//    public function getSize(): string
    {
        $size = strlen(serialize($this->value));
        return strlen($size) + $size;
    }
    public function __sleep()
    {
        return ['value'];
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
       $value = serialize($value);
//        $value = serialize($this->value);
//        return $this->name . ':' . strlen($value) . ':' . $value;
        return $this->name . ':' . sizeof($value) . ':' . $value;
    }



    /**
//     * @param string $value
     * @return newBase
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
//        $obj =new newBase($arValue[0]);
//
//        $obj->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
//            + strlen($arValue[1]) + 1), $arValue[1]));
//        return $obj;
        return (new newBase($arValue[0]))
            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1), $arValue[1]));
    }
}
class newView extends newBase
{
    private $type = null;
//    private ?string $type = null;
//    private int $size = 0;
    private $size = 0;
    private $property = null;
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        parent::setValue($value);
        $this->setType();
        $this->setSize();
    }
//    public function setProperty($value): newView
    public function setProperty($value)
    {
        $this->property = $value;
        return $this;
    }
    private function setType()
    {
        $this->type = gettype($this->value);
    }
    private function setSize()
    {
       if (is_subclass_of($this->value, "Test3\newView")) {
//        if (is_subclass_of($this->value, "Test3\\newView")) {
           $this->size = parent::getSize() + 1 + strlen($this->property);
//            $this->size = parent::getSize()::string + 1 + strlen($this->property);
        } elseif ($this->type == 'test') {
            $this->size = parent::getSize();
        } else {
            $this->size = strlen($this->value);
        }
    }
    /**
     * @return string
     */
//    /**
//     * @return string[]
//     */
    public function __sleep()
    {
        return ['property'];
    }

    /**
     * @return string
     */
//     * @throws Exception
//     */
    public function getName(): string
    {
        if (empty($this->name)) {
            throw new Exception('The object doesn\'t have name');
        }
        return '"' . $this->name  . '": ';
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return ' type ' . $this->type  . ';';
    }
    /**
     * @return string
     */
    public function getSize():string
    {
        return ' size ' . $this->size . ';';
    }
    public function getInfo()
    {
        try {
            echo $this->getName()
                . $this->getType()
                . $this->getSize()
                . "\r\n";
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
        }
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        if ($this->type == 'test') {
            $this->value = $this->value->getSave();
        }
        return parent::getSave() . serialize($this->property);
    }
    /**
     * @return newView
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
//        $obj=new newBase($arValue[0]);
//        $obj->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
//            + strlen($arValue[1]) + 1), $arValue[1]));
//        $obj->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1
//               + strlen($arValue[1]) + 1 + $arValue[1])));
//        return $obj;
        return (new newBase($arValue[0]))
            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1), $arValue[1]))
            ->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1 + $arValue[1])));
    }
}
function gettype($value): string
{
    if (is_object($value)) {
        $type = get_class($value);
        do {
//                        if (strpos($type, "Test3\\newBase") !== false) {
            if (strpos($type, "Test3\newBase") !== false) {
                return 'test';
            }
        } while ($type = get_parent_class($type));
    }
    return gettype($value);
}


$obj = new newBase('12345');
$obj->setValue('text');

$obj2 = new \Test3\newView('O9876');
$obj2->setValue($obj);
$obj2->setProperty('field');
$obj2->getInfo();

$save = $obj2->getSave();

$obj3 = newView::load($save);

var_dump($obj2->getSave() == $obj3->getSave());

