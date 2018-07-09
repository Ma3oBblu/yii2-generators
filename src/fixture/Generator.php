<?php
namespace ma3obblu\gii\generators\fixture;

use yii\db\ActiveRecord;
use yii\gii\CodeFile;

/**
 * Class Generator
 * @package ma3obblu\gii\generators\fixture
 *
 * @property string $modelClass
 * @property string $fixtureClass
 * @property string $fixtureNs
 * @property string $dataFile
 * @property string $dataPath
 * @property boolean $grabData
 */
class Generator extends \yii\gii\Generator
{
    public $modelClass;
    public $fixtureClass;
    public $fixtureNs = 'tests\fixtures';
    public $dataFile;
    public $dataPath = '@tests/fixtures/data';
    public $grabData = false;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Fixture Generator And Data Grabber';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates fixture class for existing model class with relations and prepares fixture data files.';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $files[] = new CodeFile(
            \Yii::getAlias('@' . str_replace('\\', '/', $this->fixtureNs)) . '/' . $this->getFixtureClassName() . '.php',
            $this->render('class.php')
        );

        $files[] = new CodeFile(
            \Yii::getAlias($this->dataPath) . '/' . $this->getDataFileName(),
            $this->render('data.php', ['items' => $this->getFixtureData()])
        );

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['modelClass', 'fixtureClass', 'fixtureNs', 'dataPath'], 'filter', 'filter' => 'trim'],
            [['modelClass', 'fixtureNs', 'dataPath'], 'required'],
            [['modelClass', 'fixtureNs'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['fixtureClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['dataFile'], 'match', 'pattern' => '/^\w+\.php$/', 'message' => 'Only php files are allowed.'],
            [['modelClass'], 'validateClass', 'params' => ['extends' => ActiveRecord::class]],
            [['dataPath'], 'match', 'pattern' => '/^@?\w+[\\-\\/\w]*$/', 'message' => 'Only word characters, dashes, slashes and @ are allowed.'],
            [['dataPath'], 'validatePath'],
            [['grabData'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'modelClass' => 'Model Class',
            'fixtureClass' => 'Fixture Class Name',
            'fixtureNs' => 'Fixture Class Namespace',
            'dataFile' => 'Fixture Data File',
            'dataPath' => 'Fixture Data Path',
            'grabData' => 'Grab Existing DB Data',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['class.php', 'data.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['fixtureNs', 'dataPath']);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'modelClass' => 'This is the model class. You should provide a fully qualified class name, e.g., <code>app\models\Post</code>.',
            'fixtureClass' => 'This is the name for fixture class, e.g., <code>PostFixture</code>.',
            'fixtureNs' => 'This is the namespace for fixture class file, e.g., <code>tests\fixtures</code>.',
            'dataFile' => 'This is the name for the generated fixture data file, e.g., <code>post.php</code>.',
            'dataPath' => 'This is the root path to keep the generated fixture data files. You may provide either a directory or a path alias, e.g., <code>@tests/fixtures/data</code>.',
            'grabData' => 'If checked, the existed data from database will be grabbed into data file.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        $output = <<<EOD
<p>The fixture has been generated successfully.</p>
<p>To access the data, you need to add this to your test class:</p>
EOD;
        $id = $this->getFixtureId();
        $class = $this->fixtureNs . '\\' . $this->getFixtureClassName();
        $file = $this->dataPath . '/' . $this->getDataFileName();
        $code = <<<EOD
<?php

public function fixtures()
{
    return [
        '{$id}' => [
            'class' => \\{$class}::class,
            'dataFile' => '{$file}',
        ],
    ];
}
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>';
    }

    /**
     * @param string $attribute
     */
    public function validatePath(string $attribute)
    {
        $path = \Yii::getAlias($this->$attribute, false);
        if ($path === false || !is_dir($path)) {
            $this->addError($attribute, 'Path does not exist.');
        }
    }

    /**
     * @return string
     */
    public function getDataFileName() : string
    {
        if (!empty($this->dataFile)) {
            return $this->dataFile;
        } else {
            return strtolower(pathinfo(str_replace('\\', '/', $this->modelClass), PATHINFO_BASENAME)) . '.php';
        }
    }

    /**
     * @return string
     */
    public function getFixtureClassName() : string
    {
        if (!empty($this->fixtureClass)) {
            return $this->fixtureClass;
        } else {
            return pathinfo(str_replace('\\', '/', $this->modelClass), PATHINFO_BASENAME) . 'Fixture';
        }
    }

    /**
     * @return string
     */
    public function getModelClassName() : string
    {
        return mb_substr($this->modelClass, strripos($this->modelClass, '\\') + 1);
    }

    /**
     * @return string
     */
    public function getFixtureId() : string
    {
        return strtolower(pathinfo(str_replace('\\', '/', $this->modelClass), PATHINFO_BASENAME));
    }

    /**
     * @return array
     */
    protected function getFixtureData()
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $items = [];
        if ($this->grabData) {
            $orderBy = array_combine($modelClass::primaryKey(), array_fill(0, count($modelClass::primaryKey()), SORT_ASC));
            foreach ($modelClass::find()->orderBy($orderBy)->asArray()->each() as $row) {
                $item = [];
                foreach ($row as $name => $value) {
                    if (is_null($value)) {
                        $encValue = 'null';
                    } elseif (preg_match('/^(0|[1-9-]\d*)$/s', $value)) {
                        $encValue = $value;
                    } else {
                        $encValue = var_export($value, true);;
                    }
                    $item[$name] = $encValue;
                }
                $items[] = $item;
            }
        } else {
            $item = [];
            foreach ($modelClass::getTableSchema()->columns as $column) {
                $item[$column->name] = $column->allowNull ? 'null' : '\'\'';
            }
            $items[] = $item;
        }
        return $items;
    }
}
