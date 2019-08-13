<?php

namespace App\Admin\Forms\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Encore\Admin\Form\Field\ImageField;

class Images extends Files
{
    use ImageField;

    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.multiplefile';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = 'image';



    /**
     * Prepare for each file.
     *
     * @param UploadedFile $image
     *
     * @return mixed|string
     */
    protected function prepareForeach(UploadedFile $image = null)
    {
        $this->name = $this->getStoreName($image);

        $this->callInterventionMethods($image->getRealPath());

        return tap($this->upload($image), function () {
            $this->name = null;
        });
    }
}
