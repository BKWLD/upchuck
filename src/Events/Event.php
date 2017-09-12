<?php

namespace Bkwld\Upchuck\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class Event {
	use SerializesModels;


    /**
     * A model instance
     *
     * @var
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param  Model  $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
