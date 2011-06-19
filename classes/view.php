<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Acts as an object wrapper for HTML pages with embedded PHP, called "views".
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 *
 * @package    Smarty3
 * @author     Mr Anchovy
 * @copyright  (c) 2011 Mr Anchovy
 * @license    http://kohanaframework.org/license
 */
class View extends Kohana_View {

/**
 * Sets the initial view filename and local data. Views should almost
 * always only be created using [View::factory].
 *
 *     $view = new View($file);
 *
 * @param   string  view filename
 * @param   array   array of values
 * @return  void
 * @uses    View::set_filename
 */
public function __construct($file = NULL, array $data = NULL) {
  if ( self::is_smarty_template($file) ) {
    throw new Kohana_Exception('Cannot initialise Smarty template :tpl as new View; use View::factory instead',
    array(':tpl' => $file));
  }
  return parent::__construct($file, $data);
}

/**
 * Returns a new View object. If you do not define the "file" parameter,
 * you must call [View::set_filename].
 *
 *     $view = View::factory($file);
 *
 * @param   string  view filename
 * @param   array   array of values
 * @return  View
 */
public static function factory($file = NULL, array $data = NULL) {
  // backwards compatibility - translate smarty:template to template.tpl
  if ( substr($file, 0, 7)=='smarty:') {
    $file = substr($file, 7).'.tpl';
  }
  if ( self::is_smarty_template($file) ) {
    return Smarty_View::factory($file, $data);
  } else {
    return parent::factory($file, $data);
  }
}

/**
 * Identify Smarty template file
 *
 * @param   string  template filename
 * @return  bool    TRUE iff file appears to be a Smarty template
 */
public static function is_smarty_template($file) {
  return substr($file, -4, 4)=='.tpl';
}

/**
 * Sets the view filename.
 *
 *     $view->set_filename($file);
 *
 * @param   string  view filename
 * @return  View
 * @throws  Kohana_View_Exception
 */
public function set_filename($file) {
  if ( self::is_smarty_template($file) ) {
    throw new Kohana_Exception('Cannot use set_filename to initialise Smarty template :tpl; use View::factory instead',
    array(':tpl' => $file));
  }
  return parent::set_filename($file);
}

/**
 * Sets a global variable, similar to [View::set], except that the
 * variable will be accessible to all views.
 *
 *     View::set_global($name, $value);
 *
 * @param   string  variable name or an array of variables
 * @param   mixed   value
 * @return  void
 */
public static function set_global($key, $value = NULL) {
  if ( is_array($key) ) {
    foreach ($key as $key2=>$value) {
      View::$_global_data[$key2] = $value;
      Smarty_View::smarty_prototype()->assignGlobal($key2, $value);
    }
  } else {
    View::$_global_data[$key] = $value;
    Smarty_View::smarty_prototype()->assignGlobal($key, $value);
  }
}

}
