<?php

namespace DeveoDK\CoreTimeTracker\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;

abstract class BaseService
{
    /** @var DatabaseManager */
    protected $database;

    /** @var Dispatcher */
    protected $dispatcher;

    /** @var TransformerAbstract */
    protected $transformer;

    /** @var Model */
    protected $model;

    /** @var OptionService */
    protected $optionService;

    /**
     * BaseService constructor.
     * @param Dispatcher $dispatcher
     * @param DatabaseManager $database
     * @param OptionService $optionService
     */
    public function __construct(Dispatcher $dispatcher, DatabaseManager $database, OptionService $optionService)
    {
        $this->optionService = $optionService;
        $this->database = $database;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $data
     * @return array
     */
    public function transformItem($data)
    {
        $include =  isset($_GET['include']) ? $_GET['include'] : '';

        $resource = fractal()->item($data)
            ->parseIncludes($include)
            ->transformWith($this->transformer)
            ->toArray();

        return $resource;
    }

    /**
     * @param $data
     * @return array
     */
    public function transformCollection($data)
    {
        $include =  isset($_GET['include']) ? $_GET['include'] : '';

        $resource = fractal()->collection($data)
            ->parseIncludes($include)
            ->transformWith($this->transformer)
            ->toArray();

        return $resource;
    }

    /**
     * @param $data
     * @return array
     */
    public function transformCollectionPaginate($data)
    {
        $include =  isset($_GET['include']) ? $_GET['include'] : '';

        $Collection = $data->getCollection();

        $resource = fractal()->collection($Collection)
            ->paginateWith(new IlluminatePaginatorAdapter($data))
            ->parseIncludes($include)
            ->transformWith($this->transformer)
            ->toArray();

        return $resource;
    }

    /**
     * @param TransformerAbstract $transformer
     * @return $this
     */
    public function setTransformer(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}
