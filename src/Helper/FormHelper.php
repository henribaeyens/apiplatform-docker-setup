<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\Form\FormInterface;

class FormHelper
{
    /**
     * Recursive helper who return all found errors in a form.
     */
    public static function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error;
        }

        foreach ($form->all() as $key => $child) {
            if ($child instanceof FormInterface) {
                /**
                 * @var FormInterface $child
                 */
                $err = self::getFormErrors($child);
                if (count($err) > 0) {
                    $errors = array_merge($errors, $err);
                }
            }
        }

        return $errors;
    }
}
