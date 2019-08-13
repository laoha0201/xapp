<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

trait Tree
{
    /**
     * @var array
     */
    protected static $branchOrder = [];

    /**
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * @var string
     */
    protected $titleColumn = 'title';

    /**
     * @var string
     */
    protected $orderColumn = 'order';

    /**
     * @var \Closure
     */
    protected $queryCallback;

    /**
     * Get children of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, $this->parentColumn);
    }

    /**
     * Get parent of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, $this->parentColumn);
    }

    /**
     * @return string
     */
    public function getParentColumn()
    {
        return $this->parentColumn;
    }

    /**
     * Set parent column.
     *
     * @param string $column
     */
    public function setParentColumn($column)
    {
        $this->parentColumn = $column;
    }

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return $this->titleColumn;
    }

    /**
     * Set title column.
     *
     * @param string $column
     */
    public function setTitleColumn($column)
    {
        $this->titleColumn = $column;
    }

    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     * Set order column.
     *
     * @param string $column
     */
    public function setOrderColumn($column)
    {
        $this->orderColumn = $column;
    }

    /**
     * Set query callback to model.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery(\Closure $query = null)
    {
        $this->queryCallback = $query;

        return $this;
    }

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree()
    {
        return $this->buildNestedArray();
    }

    /**
     * Build Nested array.
     *
     * @param array $nodes
     * @param int   $parentId
     *
     * @return array
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0)
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            if ($node[$this->parentColumn] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->getKeyName()]);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);
        $byOrder = $orderColumn.' = 0,'.$orderColumn;

        $self = new static();

        if ($this->queryCallback instanceof \Closure) {
            $self = call_user_func($this->queryCallback, $self);
        }

        return $self->orderByRaw($byOrder)->get()->toArray();
    }

    /**
     * Set the order of branches in the tree.
     *
     * @param array $order
     *
     * @return void
     */
    protected static function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));

        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    /**
     * Save tree order from a tree like array.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public static function saveOrder($tree = [], $parentId = 0)
    {
        if (empty(static::$branchOrder)) {
            static::setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            $node = static::find($branch['id']);

            $node->{$node->getParentColumn()} = $parentId;
            $node->{$node->getOrderColumn()} = static::$branchOrder[$branch['id']];
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch['id']);
            }
        }
    }

    /**
     * Get options for Select field in form.
     *
     * @param \Closure|null $closure
     * @param string        $rootText
     *
     * @return array
     */
    public static function selectOptions(\Closure $closure = null, $rootText = 'ROOT',$dir=false)
    {
        if($dir){
			$options = (new static())->withQuery($closure)->buildSelectOptions2();
		}else{
			$options = (new static())->withQuery($closure)->buildSelectOptions();
		}
		if($rootText === false){
			return collect($options)->all();
		}else{
			return collect($options)->prepend($rootText, '')->all();
		}
    }

    /**
     * Build options of select field in form.
     *
     * @param array  $nodes
     * @param int    $parentId
     * @param string $prefix
     * @param string $space
     *
     * @return array
     */
    protected function buildSelectOptions(array $nodes = [], $parentId = 0, $prefix = '', $space = '&nbsp;')
    {
        $prefix = $prefix ?: '┝'.$space;

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $index => $node) {
            if ($node[$this->parentColumn] == $parentId) {
                $node[$this->titleColumn] = $prefix.$space.$node[$this->titleColumn];

                $childrenPrefix = str_replace('┝', str_repeat($space, 6), $prefix).'┝'.str_replace(['┝', $space], '', $prefix);

                $children = $this->buildSelectOptions($nodes, $node[$this->getKeyName()], $childrenPrefix);

                $options[$node[$this->getKeyName()]] = $node[$this->titleColumn];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

    protected function buildSelectOptions2(array $nodes = [], $parentId = 0, $prefix = '')
    {

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $index => $node) {
            if ($node[$this->parentColumn] == $parentId) {
                //$node[$this->titleColumn] = $node[$this->titleColumn];
                $childrenPrefix = $prefix.$node[$this->titleColumn].' / ';

                $children = $this->buildSelectOptions2($nodes, $node[$this->getKeyName()], $childrenPrefix);

                if ($children) {
                    $options += $children;
                }else{
					$options[$node[$this->getKeyName()]] = $prefix.$node[$this->titleColumn];
				}
            }
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $this->where($this->parentColumn, $this->getKey())->delete();

        return parent::delete();
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Model $branch) {
            $parentColumn = $branch->getParentColumn();

            if (Request::has($parentColumn) && Request::input($parentColumn) == $branch->getKey()) {
                throw new \Exception(trans('admin.parent_select_error'));
            }

            if (Request::has('_order')) {
                $order = Request::input('_order');

                Request::offsetUnset('_order');

                static::tree()->saveOrder($order);

                return false;
            }

            return $branch;
        });
    }

    /**
     * 以下针对xapp_id处理
     */
    public function xappNodes($xapp_id = 0)
    {
		if($xapp_id){
			return $this->withQuery(function($query) use($xapp_id){
					return $query->where('xapp_id',$xapp_id);
				})->allNodes();
		}else{
			return $this->allNodes();
		}
    }

    public function getnode($parentId = 0,$xapp_id=0,$nodes=[])
    {
        $branch = [];
        if (empty($nodes)) {
            $nodes = $this->xappNodes($app_id);
        }
        foreach ($nodes as $node) {
            if ( $node['parent_id'] == $parentId ) {
                $children = $this->buildNestedArray($nodes, $node['id']);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }
        return $branch;
    }

    public function getnodeids($parentId=0,$xapp_id=0)
    {
		$nodes = $this->xappNodes($xapp_id);
		$ids[] = $parentId;
        foreach ($nodes as $k=>$node) {	
			if( in_array($node['parent_id'],$ids) ){
					$ids[]=$node['id'];
			}
        }
        return $ids;
    }
}
