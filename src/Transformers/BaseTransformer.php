<?php

namespace HttpMessagesFractalMiddleware\Transformers;

use HttpMessagesFractalMiddleware\Services\ConfigService;
use HttpMessagesFractalMiddleware\Services\ElementService;
use League\Fractal\TransformerAbstract;
use Craft\BaseElementModel;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Resource\Collection;
use RestfulApi\Transformers\ArrayTransformer;

class BaseTransformer extends TransformerAbstract
{
    /**
     * Depth
     *
     * @var integer
     */
    protected $depth = 0;

    /**
     * Config Service
     *
     * @var HttpMessagesFractalMiddleware\Services\ConfigService
     */
    protected $config_service;

    /**
     * Element Service
     *
     * @var HttpMessagesFractalMiddleware\Services\ElementService
     */
    protected $element_service;

    /**
     * Available Includes
     *
     * @var array
     */
    protected $availableIncludes = ['content'];

    public function __construct($depth = 0)
    {
        $this->depth = $depth;

        // $this->config_service  = new ConfigService;
        // $this->element_service = new ElementService;
    }

    /**
     * Include Content
     *
     * @param BaseElementModel $element Element
     *
     * @return League\Fractal\Resource\Item Content
     */
    public function includeContent(BaseElementModel $element)
    {
        $content = [];

        if ($this->depth > \Craft\craft()->config->get('contentRecursionLimit', 'httpMessagesRestMiddleware') - 1) {
            return;
        }

        foreach ($element->getFieldLayout()->getFields() as $fieldLayoutField) {
            $field = $fieldLayoutField->getField();

            $value = $element->getFieldValue($field->handle);

            if (get_class($field->getFieldType()) === 'Craft\\RichTextFieldType') {
                $value = $value->getRawContent();
            }

            if (is_object($value) && get_class($value) === 'Craft\\ElementCriteriaModel') {
                $class = get_class($value->getElementType());

                $element_type = $this->element_service->getElementTypeByClass($class);

                $manager = new Manager();
                $manager->parseIncludes(array_merge(['content'], explode(',', \Craft\craft()->request->getParam('include'))));
                $manager->setSerializer(new ArraySerializer);

                $transformer = $this->config_service->getTransformer($element_type);

                $value = $value->find();

                $body = new Collection($value, new $transformer($this->depth + 1));

                $value = $manager->createData($body)->toArray();

                $value = (!empty($value['data'])) ? $value['data'] : null;
            }

            $content[$field->handle] = $value;
        }

        if ($content) {
            return $this->item($content, new ContentTransformer($this->depth), 'content');
        }
    }

    /**
     * Call
     *
     * @param string $method Method
     * @param mixed  $args   Args
     *
     * @return void
     */
    public function __call($method, $args)
    {
        return;
    }

}
