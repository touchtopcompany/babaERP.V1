<?php

namespace App\Action;

use App\Enums\LabelType;

class BusinessCustomLabelsAction
{
    //Constructor promotion for Php >=8.0
    public function __construct( protected $util)
    {
    }

    public function handle(int $business_id, string $label_type)
    {
        switch ($label_type)
        {
            case LabelType::PAYMENT:
            case LabelType::CONTACT:
            case LabelType::PRODUCT:
            $custom_labels = $this->util->getCustomLabels($business_id, $label_type);
            $labels = [];
            if (!empty($custom_labels))
                foreach ($custom_labels as $key => $label)
                    if (!is_null($label)) $labels[$key] = $label;
            return $labels;
            default:
                abort(code: response("Unrecognized label '{$label_type}'",500));
        }
    }
}