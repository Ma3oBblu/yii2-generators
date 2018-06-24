<?php
use yii\web\View;
use yii\base\Model;
use ma3obblu\gii\generators\form\Generator;
use ma3obblu\gii\generators\helpers\GeneratorHelper;

/** @var $this View */
/** @var $generator Generator */

/** @var Model $entity */
$entity = new $generator->modelClass;
echo "<?php\n";
?>
namespace <?= $generator->getNamespace() . "\\" . $generator->formUrl ?>;

use yii\base\Model;
use <?= $generator->modelClass; ?>;

/**
 * Class <?= $generator->getFormClassName() . "\n"; ?>
 * @package <?= $generator->getNamespace() . "\\" . $generator->formUrl . "\n *\n"; ?>
<?= GeneratorHelper::generatePhpDocForClassAttributes($entity); ?>
 */
class <?= $generator->getFormClassName(); ?> extends Model
{
<?= GeneratorHelper::generateClassParams($entity); ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
<?= GeneratorHelper::convertRules($entity);?>
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?= GeneratorHelper::generateAttributeLabels($entity); ?>
        ];
    }

<?= GeneratorHelper::generateFormConstructor($entity); ?>
}