<?php
use yii\web\View;
use ma3obblu\gii\generators\fixture_class\Generator;

/** @var $this View */
/** @var $generator Generator */
/** @var $items array */

echo "<?php\n";
?>
return [
<?php if (!empty($items)){ foreach ($items as $item){ ?>
<?= $item; ?>
<?php } ?>
<?php } ?>
];