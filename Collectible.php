<?php

/**
 * @author Ashish Kumar
 * @desc Singular Model class for GamificationCollectible DB class
 */
class Gamification_Collectible extends Gamification_AbstractSingular {

    protected $_dbClass = 'Collectible';
    protected $_foreignKey = 'collectibleId';

}

?>
