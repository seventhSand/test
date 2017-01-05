<?php

/**
 * Created by PhpStorm
 * Date: 19/10/2016
 * Time: 16:32
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */
class PostController
{
    use Input;

    protected $post;

    public function __construct()
    {
        $this->post = Input::post();
    }

    public function actionSave()
    {
//        Get configuration pair
//        Separate post based on their table pair name
//        Validate post based on their rules (do this on server side)
//        Save transaction per table, started by master table
//        Record transaction
//        Set transaction message
//        Return results
    }

    public function actionUpdate()
    {

    }

    public function actionDelete()
    {

    }
}