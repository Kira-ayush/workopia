<?php
/**
 *get the base path
 * @param string $path
 * @return string
 */

 function basePath($path=""){
    return __DIR__ .'/'.$path;
 }
 /**
 * load a view
 * @param string $name
 * @return void
 */

 function loadView($name,$data=[])
 {
    $viewPath=basePath("App/views/{$name}.view.php");

    if(file_exists($viewPath)){
      extract($data);
    require $viewPath; 
    }else{
        echo "view {$name} not exists";
    }
    
 }
  /**
 * load a partial view
 * @param string $name
 * @return void
 */

 function loadPartialView($name){
    $viewPartialPath= basePath("App/views/partials/{$name}.php");
    if(file_exists($viewPartialPath)){
    require $viewPartialPath; 
    }else{
        echo "view {$name} not exists";
    }
 }
 
/**
 * Inspect a value(s)
 * 
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

/**
 * Inspect a value(s) and die
 * 
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value)
{
  echo '<pre>';
  die(var_dump($value));
  echo '</pre>';
}

/**
 * 
 * Format Salary
 * 
 * @param string $salary
 * @return string Format
 */
function formatSalary($salary){
   return '$' . number_format(floatval($salary));
}

/**
 * Sanitize data
 * 
 * @param string $dirty
 * 
 * @return string
 * 
 */
function sanitize($dirty){
   return filter_var(trim($dirty),FILTER_SANITIZE_SPECIAL_CHARS);
}
/**
 * Redirect to a given url
 * @param string $url
 * @return void
 * 
 */
function redirect($url){
   header("Location:{$url}");
   exit;
}