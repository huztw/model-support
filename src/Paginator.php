<?php

namespace Huztw\ModelSupport;

use Illuminate\Pagination\Paginator as PaginationPaginator;

class Paginator
{
    /**
     * 資料庫構造器
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    public $builder;

    /**
     * 分頁結果
     *
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    protected $paginated;

    /**
     * 修改結果閉包
     *
     * @var array
     */
    protected $transforms = [];

    /**
     * 資料庫欄位
     *
     * @var array
     */
    public $columns = ['*'];

    /**
     * 頁碼名稱
     *
     * @var string
     */
    public $pageName = 'page';

    /**
     * 當前頁碼
     *
     * @var int
     */
    public $page = 1;

    /**
     * 每頁顯示的項目數
     *
     * @var int
     */
    public $perPage = 10;

    /**
     * Create a new paginator instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
        $this->page(PaginationPaginator::resolveCurrentPage());
    }

    /**
     * 取得查詢結果
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get()
    {
        $paginated = $this->builder->paginate($this->perPage, $this->columns, $this->pageName, $this->page);

        $this->paginated = count($this->transforms) > 0 ? tap($paginated, function ($paginatedInstance) {
            $paginatedInstance->getCollection()->transform(function ($item, $key) {
                foreach ($this->transforms as $transform) {
                    call_user_func($transform, $item, $key);
                }

                return $item;
            });
        }) : $paginated;

        return $this->paginated;
    }

    /**
     * 取得查詢結果集合
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function collection()
    {
        return optional($this->paginated ?? $this->get())->getCollection();
    }

    /**
     * 設定資料庫欄位
     *
     * @param  array $columns
     *
     * @return $this
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * 設定頁碼名稱
     *
     * @param  string $name
     *
     * @return $this
     */
    public function pageName($name)
    {
        $this->pageName = $name;

        return $this;
    }

    /**
     * 設定當前頁碼
     *
     * @param  int $num
     *
     * @return $this
     */
    public function page($num)
    {
        $this->page = $num;

        return $this;
    }

    /**
     * 設定每頁顯示的項目數
     *
     * @param  int $num
     *
     * @return $this
     */
    public function perPage($num)
    {
        if (is_numeric($num)) {
            $this->perPage = intval($num);
        } elseif ($num !== null && filter_var($num, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === false) {
            $this->perPage = $this->builder->count();
        }

        return $this;
    }

    /**
     * 設定修改結果閉包，傳入數組可覆寫現有的閉包
     *
     * @param  array|callable $callback
     *
     * @return $this
     */
    public function transform($callback)
    {
        if (is_array($callback)) {
            $this->transforms[key($callback)] = current($callback);
        } else {
            array_push($this->transforms, $callback);
        }

        return $this;
    }

    /**
     * 刪除一個或多個數組項的閉包
     *
     * @param array|string $keys
     *
     * @return $this
     */
    public function forgetTransform($keys)
    {
        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $this->transforms)) {
                unset($this->transforms[$key]);
            }
        }

        return $this;
    }
}
