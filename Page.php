<?php
/**
 * @Author: [FENG] <1161634940@qq.com>
 * @Date:   2019-02-20T15:06:21+08:00
 * @Last Modified by:   [FENG] <1161634940@qq.com>
 * @Last Modified time: 2019-02-20T17:16:49+08:00
 */
namespace feng;

use think\Paginator;

class Page extends Paginator
{

    //首页
    protected function home() {
        if ($this->currentPage() > 1) {
            return "<a class='first' href='" . $this->url(1) . "' title='首页'>首页</a>";
        } else {
            return "<p class='first'>首页</p>";
        }
    }

    //上一页
    protected function prev() {
        if ($this->currentPage() > 1) {
            return "<a href='" . $this->url($this->currentPage - 1) . "' title='上一页'>上一页</a>";
        } else {
            // return "<p>上一页</p>";
        }
    }

    //下一页
    protected function next() {
        if ($this->hasMore) {
            return "<a class='next' href='" . $this->url($this->currentPage + 1) . "' title='下一页'>下一页</a>";
        } else {
            // return"<p>下一页</p>";
        }
    }

    //尾页
    protected function last() {
        if ($this->hasMore) {
            return "<a class='last' href='" . $this->url($this->lastPage) . "' title='尾页'>尾页</a>";
        } else {
            return "<p class='last'>尾页</p>";
        }
    }

    //统计信息
    protected function info(){
        return "<p class='pageRemark'>共<b>" . $this->lastPage .
            "</b>页<b>" . $this->total . "</b>条数据</p>";
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 3;
        $window = $side * 2;

        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            $block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '%s<div class="pagination">%s %s %s</div>',
                    $this->css(),
                    $this->prev(),
                    $this->getLinks(),
                    $this->next()
                );
            } else {
                return sprintf(
                    '%s<div class="pagination">%s %s %s %s %s %s</div>',
                    $this->css(),
                    $this->home(),
                    $this->prev(),
                    $this->getLinks(),
                    $this->next(),
                    $this->last(),
                    $this->info()
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<a href="' . htmlentities($url) . '" title="第"'. $page .'"页" >' . $page . '</a>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<p class="pageEllipsis">' . $text . '</p>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<a href="" class="cur">' . $text . '</a>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    /**
     * 分页样式
     */
    protected function css($color='077ee3'){
        if ($this->simple) {
            $css = '
            .pagination .next{
                float: right;
            }
            .pagination a {
              border-radius: 4px;
            }
            ';
        } else {
            $css = '
            .pagination a.cur{
                background:#'.$color.';
                color:#fff;
                border: 1px #'.$color.' solid;
            }
            .pagination p{
                float:left;
                margin:0px 10px 0px 0px;
                padding:2px 12px;
                font-size:12px;
                height:24px;
                line-height:24px;
                color:#cccccc;
                border:1px #cccccc solid;
            }
            .pagination p:hover{
                cursor: not-allowed;
            }
            .pagination p.pageRemark{
                border-style:none;
                background:none;
                margin-right:0px;
                padding:4px 0px;
                color:#666;
            }
            .pagination p.pageRemark b{
                color:red;
            }
            .pagination p.pageEllipsis{
                border-style:none;
                background:none;
                padding:4px 0px;
                color:#808080;
            }
            .pagination .first {
              margin-left: 0;
              border-top-left-radius: 4px;
              border-bottom-left-radius: 4px;
            }
            .pagination .last {
              border-top-right-radius: 4px;
              border-bottom-right-radius: 4px;
            }';
        }
        return '<style type="text/css">
        .pagination p{
            margin:0;
            cursor:pointer
        }
        .pagination{
            height:40px;
            padding:20px 0px;
        }
        .pagination a{
            display:block;
            float:left;
            margin:0px 10px 0px 0px;
            padding:2px 12px;
            height:24px;
            border:1px #cccccc solid;
            background:#fff;
            text-decoration:none;
            color:#'.$color.';
            font-size:12px;
            line-height:24px;
        }
        .pagination a:hover{
            color:#'.$color.';
            background: #fff;
            // border:1px #cccccc solid;
            border: 1px #'.$color.' solid;
        }
        '.$css.'</style>';
    }
}
