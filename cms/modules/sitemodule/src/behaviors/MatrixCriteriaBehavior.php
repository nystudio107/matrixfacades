<?php
/**
 * Site module for Craft CMS 3.x
 *
 * Custom site module
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2022 nystudio107
 */

namespace modules\sitemodule\behaviors;

use Craft;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\elements\MatrixBlock;
use craft\events\CancelableEvent;

use yii\base\Behavior;

class MatrixCriteriaBehavior extends Behavior
{
    // Constants
    // =========================================================================

    const NO_MATCHING_MATRIX_CRITERIA = 'no-matching-matrix-criteria';

    // Public Properties
    // =========================================================================

    /**
     * @var string the Matrix field to use
     */
    public $matrixFieldHandle;

    /**
     * @var array the criteria for the Matrix query
     */
    public $matrixCriteria;

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function events()
    {
        return [
            ElementQuery::EVENT_BEFORE_PREPARE => function($event) {
                $this->applyMatrixCriteriaParams($event);
            },
        ];
    }

    /**
     * Limit the ElementQuery to elements that match the passed in Matrix criteria
     *
     * @param string $matrixFieldHandle the handle of the Matrix field to match the criteria in
     * @param array $matrixCriteria the criteria for the MatrixBlock query
     * @return ElementQueryInterface
     */
    public function matrixCriteria(string $matrixFieldHandle, array $matrixCriteria): ElementQueryInterface
    {
        $this->matrixFieldHandle = $matrixFieldHandle;
        $this->matrixCriteria = $matrixCriteria;
        /* @var ElementQueryInterface $elementQuery */
        $elementQuery = $this->owner;

        return $elementQuery;
    }

    // Private Methods
    // =========================================================================

    /**
     * Apply the 'matrixFieldHandle' & 'matrixCriteria' params to select the ids
     * of the elements that own matrix blocks that match, and then add them to the
     * id parameter of the ElementQuery
     *
     * @param CancelableEvent $event
     */
    private function applyMatrixCriteriaParams(CancelableEvent $event): void
    {
        if (!$this->matrixFieldHandle || empty($this->matrixCriteria)) {
            return;
        }
        /* @var ElementQueryInterface $elementQuery */
        $elementQuery = $this->owner;
        // Get the id of the matrix field from the handle
        $matrixField = Craft::$app->getFields()->getFieldByHandle($this->matrixFieldHandle);
        if ($matrixField === null) {
            return;
        }
        // Set up the matrix block query
        $matrixQuery = MatrixBlock::find();
        // Mix in any criteria for the matrix block query
        Craft::configure($matrixQuery, $this->matrixCriteria);
        // Get the ids of the elements that contain matrix blocks that match the matrix block query
        $ownerIds = $matrixQuery
            ->fieldId($matrixField->id)
            ->select('matrixblocks.ownerId')
            ->orderBy(null)
            ->distinct()
            ->column();
        // If the original query's `id` is not empty, use the intersection
        if (!empty($elementQuery->id)) {
            $originalIds = $elementQuery->id;
            if (!is_array($originalIds)) {
                $originalIds = [(int)$originalIds];
            }
            $ownerIds = array_intersect($originalIds, $ownerIds);
        }
        // Ensure the parent query returns nothing if no ids were found
        if (empty($ownerIds)) {
            $ownerIds = null;
            $elementQuery->uid = self::NO_MATCHING_MATRIX_CRITERIA;
        }
        // Add them to the original query that was passed in
        $elementQuery->id($ownerIds);
    }
}
