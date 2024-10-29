<?php

namespace SSA\Actions;

use SSA\Interfaces\Runnable;

class Abstract_Action implements Runnable {

    /**
     * string @var
     */
    protected $id;

    /**
     * string @var
     */
    protected $title;

    /**
     * string @var
     */
    protected $description;


    public function run() {
    }

    public function id() {
        return $this->id;
    }

    public function title() {
        return $this->title;
    }

    public function description() {
        return $this->description;
    }
}
