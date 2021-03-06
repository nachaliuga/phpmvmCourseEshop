<?php
use Philo\Blade\Blade;
use voku\helper\Paginator;
use Illuminate\Database\Capsule\Manager as Capsule;


function view($path, array $data = [])
{
    $view = __DIR__.'/../../resources/views';
    $cache = __DIR__.'/../../bootstrap/cache';
    $blade = new Blade($view, $cache);

    echo $blade->view()->make($path, $data)->render();
}
function make($filename, $data)
{
    extract($data);
    ob_start();
    //include template
    include(__DIR__.'/../../resources/views/emails/'.$filename.'.php');
    // get content
    $content = ob_get_contents();
    //erease the output and turn of output buffering
    ob_end_clean();

    return $content;
}
function slug($value)
{
    // preg_replace performs regular search preg_quote used to quote regular expression
    // remove all characters not in this list: underscore | letters | numbers | whitespace
    $value = preg_replace('![^'.preg_quote('_').'\pL\pN\s]+!u', '', mb_strtolower($value));
    //replace underscore and whitespace with a dash -
    $value = preg_replace('!['.preg_quote('-').'\s]+!u', '-', $value);
    //remove whitespace
    return trim($value, '-');
}
function paginate($num_of_records, $total_record, $table_name, $object)
{

    $pages = new Paginator($num_of_records, 'p');
    $pages->set_total($total_record);
    $data = Capsule::select("SELECT * FROM $table_name WHERE deleted_at is null
                            ORDER BY created_at DESC " . $pages->get_limit());

    $categories = $object->transform($data);

    return [$categories, $pages->page_links()];
}
