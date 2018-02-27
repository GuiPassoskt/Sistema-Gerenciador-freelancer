<?php

class Expense extends ActiveRecord\Model {
    static $belongs_to = array(
    array('project'),
    array('user'),
    array('invoice'),
    );

}