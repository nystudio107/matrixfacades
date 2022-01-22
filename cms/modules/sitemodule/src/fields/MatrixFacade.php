<?php

namespace modules\sitemodule\fields;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\MatrixBlock;
use craft\fields\Matrix;
use craft\helpers\Html;
use craft\helpers\StringHelper;

class MatrixFacade extends Matrix implements FacadeInterface
{
    // Traits
    // ===================================================================================

    use FacadeTrait;

    // Public Static Methods
    // ===================================================================================

    /**
     * @var string path to the input template for the matrix facade
     */
    public $inputTemplatePath;

    // Public Properties
    // ===================================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'Matrix Facade');
    }

    // Public Methods
    // ===================================================================================

    /**
     * @inheritdoc
     */
    public function getStaticHtml($value, ElementInterface $element): string
    {
        // See if we should render as a Matrix field
        if ($this->disableMatrixFacades()) {
            return parent::getStaticHtml($value, $element);
        }

        /** @var MatrixBlockQuery $value */
        $value = $value->all();

        /** @var MatrixBlock[] $value */
        if (empty($value)) {
            return '<p class="light">' . Craft::t('app', 'No blocks.') . '</p>';
        }

        $id = StringHelper::randomString();

        return Craft::$app->getView()->renderTemplate(
            $this->inputTemplatePath,
            [
                'id' => $id,
                'name' => $id,
                'blocks' => $value,
                'static' => true,
                'element' => $element,
            ]);
    }

    // Protected Methods
    // ===================================================================================

    /**
     * Should we disable facades, and display regular Matrix blocks?
     *
     * @return bool
     */
    protected function disableMatrixFacades(): bool
    {
        // See if facades have been globally disabled
        if ($this->disableFacades()) {
            return true;
        }

        // Ensure there is a value specified in inputTemplatePath
        if (empty($this->inputTemplatePath)) {
            return true;
        }

        // Check permissions
        $currentUser = Craft::$app->getUser()->getIdentity();
        if ($currentUser->disableMatrixFacades->contains('disableMatrixFacades')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        $matrixSettings = parent::getSettingsHtml();

        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
                'site-module/_components/fields/MatrixFacade/settings',
                [
                    'matrixField' => $this,
                ]
            ) . $matrixSettings;
    }

    /**
     * @inheritdoc
     */
    protected function inputHtml($value, ElementInterface $element = null): string
    {
        // See if we should render as a Matrix field
        if ($this->disableMatrixFacades()) {
            return parent::inputHtml($value, $element);
        }

        if ($value instanceof MatrixBlockQuery) {
            $value = $value->getCachedResult() ?? $value->limit(null)->anyStatus()->all();
        }

        return Craft::$app->getView()->renderTemplate(
            $this->inputTemplatePath,
            [
                'id' => Html::id($this->handle),
                'name' => $this->handle,
                'blocks' => $value,
                'static' => false,
                'element' => $element,
            ]);
    }
}
