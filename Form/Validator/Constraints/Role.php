<?php

/*
 * This file is part of the CCDNUser AdminBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNUser\AdminBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.1
 *
 * @see http://symfony.com/doc/current/cookbook/validation/custom_constraint.html
 */
class Role extends Constraint
{

    /**
     *
     * @access public
     */
    public $message = 'The role "%role%" is not valid!';

    /**
     *
     * @access public
     * @param Array() $usernames
     */
    public function addNotFoundUsernames($usernames)
    {
        if (is_array($usernames) && count($usernames) > 0) {
            $usernames = implode(", ", $usernames);

            $this->message = str_replace("%users%", $usernames, $this->message);
        } else {
            $this->message = "You must provide a valid username to receive the message.";
        }
    }

    /**
     *
     * @access public
     * @return string
     */
    public function validatedBy()
    {
        return 'role';
    }

}
