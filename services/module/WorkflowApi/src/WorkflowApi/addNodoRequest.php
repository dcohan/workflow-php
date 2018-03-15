<?php

/**
 * @SWG\Definition(required={"addNodoRequest"})
 */
class addNodoRequest {
    /**
     * @SWG\Property(format="int64", default="")
     * @var id
     */
    public $id;
     /**
     * @SWG\Property(format="string", default="new node")
     * @var nombre
     */
    public $nombre;
}
