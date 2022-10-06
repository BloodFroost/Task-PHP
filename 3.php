<?php

declare(strict_types=1);

namespace Test3;

use Exception;

class newBase
{
    static private int $count = 0;
    static private array $arSetName = [];
    /**
     * @param string $name
     */

    function __construct(string $name)
    {
        if (empty($name)) {
            while (array_search(self::$count, self::$arSetName)) {
                ++self::$count;
            }
            $name = self::$count;
        }
        $this->name = $name;
        self::$arSetName[] = $this->name;
    }

    protected string|int $name;
    /**
     * @return string
     */
    public function getName(): string
    {
        return '*' . $this->name  . '*';
    }
    protected  mixed $value;
    /**
     * @param mixed $value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string|int
     */

    public function getSize(): int|string
    {
        $size = strlen(serialize($this->value));
        return strlen((string)$size) + $size;
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
        $value = serialize($this->value);
        return $this->name . ':' . strlen($value) . ':' . $value;
    }



    /**
     * @param string $value
     * @return newBase
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
        $obj = new newBase($arValue[0]);

        $obj->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
            + strlen($arValue[1]) + 1 + $arValue[1])));
        return $obj;
    }
}
class newView extends newBase
{
    private ?string $type = null;
    protected int $size = 0;
    private  string|null $property = null;
    /**
     * @param mixed $value
     */
    public function setValue(mixed $value): void
    {
        parent::setValue($value);
        $this->setType();
        $this->setSize();
    }
    public function setProperty($value): static
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

        if (is_subclass_of($this->value, "Test3\\newView")) {
            $this->size = parent::getSize()::string + 1 + strlen((string)$this->property);
        } elseif ($this->type == 'test') {
            $this->size = parent::getSize();
        } else {
            $this->size = strlen($this->value);
        }
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return ['property'];
    }

    /**
     * @return string
     * @throws Exception
     */
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
    public function getSize(): string
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
        $obj = new newView($arValue[0]);
        $obj->setValue(unserialize(substr($value, strlen($arValue[0]) + 1 + strlen($arValue[1]) + 1 + $arValue[1])));
        $obj->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1 + strlen($arValue[1]) + 1 + $arValue[1])));
        return $obj;
    }
}
function gettype($value): string
{
    if (is_object($value)) {
        $type = get_class($value);
        do {

            if (str_contains($type, 'Test3\newBase')) {
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
