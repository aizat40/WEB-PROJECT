<?php
function paginated_rows($from, $order, $types = '', $values = [], $columns = '*')
{
    global $pagination;
    $total = (int) row('SELECT COUNT(*) AS total FROM ' . $from, $types, $values)['total'];
    $size = filter_var($_GET['per_page'] ?? 10, FILTER_VALIDATE_INT);
    if (!in_array($size, [10,25,50,100], true)) $size = 10;
    $pages = max(1, (int) ceil($total / $size));
    $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
    $page = min($pages, max(1, $page ?: 1));
    $offset = ($page - 1) * $size;
    $pagination = compact('total','size','pages','page','offset');
    return rows('SELECT ' . $columns . ' FROM ' . $from . ' ORDER BY ' . $order . ' LIMIT ? OFFSET ?', $types . 'ii', array_merge($values, [$size,$offset]));
}
function pagination_controls()
{
    global $pagination;
    if (!isset($pagination)) return;
    extract($pagination);
    $params = $_GET;
    unset($params['page'], $params['per_page']);
    $params = array_filter($params, 'is_scalar');
    $url = function ($number) use ($params, $size) { return '?' . http_build_query(array_merge($params, ['per_page'=>$size,'page'=>$number])); };
    echo '<div class="table-pagination"><span>Showing ' . ($total ? $offset + 1 : 0) . '–' . min($total, $offset + $size) . ' of ' . $total . ' records · Page ' . $page . ' of ' . $pages . '</span><form method="get" class="page-size-form">';
    foreach ($params as $key=>$value) echo '<input type="hidden" name="' . e($key) . '" value="' . e($value) . '">';
    echo '<input type="hidden" name="page" value="1"><label>Rows per page <select name="per_page" class="form-select form-select-sm">';
    foreach ([10,25,50,100] as $option) echo '<option value="'.$option.'"'.($option === $size ? ' selected' : '').'>'.$option.'</option>';
    echo '</select></label><button class="btn btn-sm btn-light">Apply</button></form><nav aria-label="Table pages"><ul class="pagination mb-0">';
    $link = function ($number, $label, $disabled = false) use ($url, $page) {
        echo '<li class="page-item'.($disabled ? ' disabled' : ($number === $page && is_int($label) ? ' active' : '')).'">';
        if ($disabled) echo '<span class="page-link" aria-disabled="true">'.e($label).'</span>';
        else echo '<a class="page-link" href="'.e($url($number)).'"'.($number === $page && is_int($label) ? ' aria-current="page"' : '').'>'.e($label).'</a>';
        echo '</li>';
    };
    $link($page-1, 'Previous', $page === 1);
    $numbers = array_unique(array_merge([1,$pages], range(max(1,$page-2),min($pages,$page+2)))); sort($numbers);
    $previous=0;
    foreach ($numbers as $number) { if ($previous && $number > $previous+1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>'; $link($number,$number); $previous=$number; }
    $link($page+1, 'Next', $page === $pages);
    echo '</ul></nav></div>';
}
