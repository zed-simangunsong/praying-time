<?php
/**
 * Copyright (c) 2024. by zed-simangunsong
 *
 * @license     MIT License
 * @copyright   Copyright (2) 2024, zed-simangunsong
 */

namespace Zed\Test\Lib;


use Pecee\Pixie\QueryBuilder\QueryBuilderHandler;

class Paginator
{
    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var QueryBuilderHandler
     */
    protected $builder;

    /**
     * @var null|string
     */
    protected $countColumn;

    /**
     * @var int Total rows.
     */
    protected $total;

    /**
     * @var int Current page.
     */
    protected $current;

    /**
     * @var int Max page
     */
    protected $max;

    /**
     * @var array Items.
     */
    protected $items;

    /**
     * @var  int Offset start.
     */
    protected $start;

    /**
     * @var int Offset end.
     */
    protected $end;

    /**
     * @var string
     */
    protected $pagingLink;


    public function __construct($perPage, QueryBuilderHandler $builder, $countColumn = null)
    {
        $this->perPage = $perPage;

        $this->builder = $builder;

        $this->countColumn = $countColumn;
    }

    /**
     * @return $this
     * @throws \Pecee\Pixie\Exception
     * @throws \Pecee\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pecee\Pixie\Exceptions\ConnectionException
     * @throws \Pecee\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pecee\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pecee\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pecee\Pixie\Exceptions\ForeignKeyException
     * @throws \Pecee\Pixie\Exceptions\NotNullException
     * @throws \Pecee\Pixie\Exceptions\TableNotFoundException
     */
    public function finalize()
    {
        // Get total row.
        $total = clone $this->builder;
        $this->total = $this->countColumn ? $total->count($this->countColumn) : $total->count();

        $this->max = ceil($this->total / $this->perPage);
        $this->start = ($this->current - 1) * $this->perPage;

        $this->items = $this->builder->limit($this->perPage)->offset($this->start)->get();

        $this->end = $this->current >= $this->max ? $this->total : $this->start + $this->perPage;
        $this->start++;


        return $this;
    }

    public function setCurrent($current)
    {
        $this->current = (int)$current;

        return $this;
    }

    public function setPagingLink($link)
    {
        $this->pagingLink = $link;

        return $this;
    }

    public function get($key, $default = null)
    {
        return isset($this->{$key}) ? $this->{$key} : $default;
    }

    /**
     * @param $view
     * @param array $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function view($view, array $data = [])
    {
        $data['paginator'] = $this->toArray();

        return view($view, $data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'total' => $this->total,
            'current' => $this->current,
            'items' => $this->items,
            'max' => $this->max,
            'start' => $this->start,
            'end' => $this->end,
            'perPage' => $this->perPage,
            'paging' => $this->paging(),
        ];
    }

    /**
     * Generate pagination links.
     *
     * @return string
     */
    protected function paging()
    {
        if ([] !== $this->pagingLink && $this->max > 1) {
            $nav = '<ul class="pagination">';
            $nav .= $this->max > 7 ? $this->usingDots() : $this->normalPage();
            $nav .= '</ul>';

            return $nav;
        }
    }

    protected function usingDots()
    {
        $list = '';
        if (1 === $this->current) {
            $list .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
            $list .= '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
        } else {
            $list .= $this->makePagingItem($this->current - 1, 'Previous');
            $list .= $this->makePagingItem(1);
        }

        if ($this->max - $this->current > 3) {
            if ($this->current > 4) {
                $list .= '<li class="page-item"><span class="page-link">...</span></li>';
                $list .= $this->makePagingItem($this->current - 1);
                $list .= '<li class="page-item active" aria-current="page"><span class="page-link">'
                    . $this->current . '</span></li>';
                $list .= $this->makePagingItem($this->current + 1);
            } else {
                for ($page = 2; $page <= 5; $page++) {
                    if ($this->current == $page) {
                        $list .= '<li class="page-item active" aria-current="page"><span class="page-link">'
                            . $page . '</span></li>';
                    } else {
                        $list .= $this->makePagingItem($page);
                    }
                }
            }
        }

        if ($this->max - $this->current < 4) {
            $list .= '<li class="page-item"><span class="page-link">...</span></li>';
            for ($page = $this->max - 4; $page <= $this->max - 1; $page++) {
                if ($this->current == $page) {
                    $list .= '<li class="page-item active" aria-current="page"><span class="page-link">'
                        . $page . '</span></li>';
                } else {
                    $list .= $this->makePagingItem($page);
                }
            }
        } else {
            $list .= '<li class="page-item"><span class="page-link">...</span></li>';
        }

        if ($this->current == $this->max) {
            $list .= '<li class="page-item active" aria-current="page"><span class="page-link">' . $this->max . '</span></li>';
            $list .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        } else {
            $list .= $this->makePagingItem($this->max);
            $list .= $this->makePagingItem($this->current + 1, 'Next');
        }

        return $list;
    }

    /**
     * Build list paging item.
     *
     * @param $page
     * @param null $text
     * @return string
     */
    protected function makePagingItem($page, $text = null)
    {
        if (!$text) {
            $text = $page;
        }

        $link = '#' === $page ? $page : str_replace(':page', $page, $this->pagingLink);

        return '<li class="page-item"><a href="' . $link . '" class="page-link">' . $text . '</a></li>';
    }

    protected function normalPage()
    {
        $list = '';

        if (1 === $this->current) {
            $list .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        } else {
            $list .= $this->makePagingItem($this->current - 1, 'Previous');
        }

        for ($page = 1; $page <= $this->max; $page++) {
            if ($this->current === $page) {
                $list .= '<li class="page-item active" aria-current="page"><span class="page-link">' . $page . '</span></li>';
            } else {
                $list .= $this->makePagingItem($page);
            }
        }

        if ($this->current == $this->max) {
            $list .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        } else {
            $list .= $this->makePagingItem($this->current + 1, 'Next');
        }

        return $list;
    }
}