<?
namespace App\Admin\Forms;

use Encore\Admin\Form as BaseForm;
use Illuminate\Support\Arr;
use Encore\Admin\Form\Field;

/**
 * Class Form.
 *
 * @method Field\Text           text($column, $label = '')
 * ...............................
 * @method Field\ListField      list($column, $label = '')
 */
class Form extends BaseForm
{
	protected $form;
	
    public function vform($method, $arguments){
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, ''); //[0];

            $element = new $className($column, array_slice($arguments, 1));

            $this->form->pushField($element);

            return $element;
        }

        admin_error('Error', "Field type [$method] does not exist.");

        return new Field\Nullable();
    }	
}