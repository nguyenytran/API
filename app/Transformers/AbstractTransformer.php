<?php


namespace App\Transformers;


use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function collection($data, $transformer, $resourceKey = null)
    {
        if (!$resourceKey) {
            throw new \Exception('Missing resource key');
        }
        return parent::collection($data, $transformer, $resourceKey);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function primitive($data, $transformer = null, $resourceKey = null)
    {
        if (!$resourceKey) {
            throw new \Exception('Missing resource key');
        }

        if (!$data) {
            return $this->null();
        }

        return parent::primitive($data, $transformer, $resourceKey);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function item($data, $transformer = null, $resourceKey = null)
    {
        if (!$resourceKey) {
            throw new \Exception('Missing resource key');
        }

        if (!$data) {
            return $this->null();
        }

        return parent::item($data, $transformer, $resourceKey);
    }
}
