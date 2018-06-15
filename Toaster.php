<?php

namespace App\Utils;

use Session;
use Illuminate\Support\HtmlString;

class Toaster {

  private static $options = [
      "closeButton" => false,
      "debug" => false,
      "newestOnTop" => false,
      "progressBar" => false,
      "positionClass" => "toast-top-right",
      "preventDuplicates" => false,
      "onclick" => null,
      "showDuration" => "3000",
      "hideDuration" => "1000",
      "timeOut" => "5000",
      "extendedTimeOut" => "1000",
      "showEasing" => "swing",
      "hideEasing" => "linear",
      "showMethod" => "fadeIn",
      "hideMethod" => "fadeOut"
  ];
  private static $toastType = "success";
  private static $instance;
  private static $title;
  private static $message;
  private static $toastTypes = ["success", "info", "warning", "error"];

  public function __construct($options = []) {
    self::$options = array_merge(self::$options, $options);
  }

  public static function setOptions(array $options = []) {
    self::$options = array_merge(self::$options, $options);

    return self::getInstance();
  }

  public static function setOption($option, $value) {
    self::$options[$option] = $value;

    return self::getInstance();
  }

  private static function getInstance() {
    if (empty(self::$instance) || self::$instance === null) {
      self::setInstance();
    }

    return self::$instance;
  }

  private static function setInstance() {
    self::$instance = new static();
  }

  public static function __callStatic($method, $args) {
    if (in_array($method, self::$toastTypes)) {
      self::$toastType = $method;

      return self::getInstance()->initToast($method, $args);
    }

    throw new \Exception("Ohh my god. That toast doesn't exists.");
  }

  public function __call($method, $args) {
    return self::__callStatic($method, $args);
  }

  private function initToast($method, $params = []) {
    if (count($params) == 2) {
      self::$title = $params[0];

      self::$message = $params[1];
    } elseif (count($params) == 1) {
      self::$title = ucfirst($method);

      self::$message = $params[0];
    }

    $toasters = [];

    if (Session::has('toasters')) {
      $toasters = Session::get('toasters');
    }

    $toast = [
        "options" => self::$options,
        "type" => self::$toastType,
        "title" => self::$title,
        "message" => self::$message
    ];

    $toasters[] = $toast;

    Session::forget('toasters');

    Session::put('toasters', $toasters);

    return $this;
  }

  public static function renderToasters() {
    $toasters = Session::get('toasters');

    $string = '';
    if (!empty($toasters)) {
      $string .= '<script type="application/javascript">';
      $string .= "$(function() {\n";
      foreach ($toasters as $toast) {
        $string .= "\n toastr.options = " . json_encode($toast['options'], JSON_PRETTY_PRINT) . ";";
        $string .= "\n toastr['{$toast['type']}']('{$toast['message']}', '{$toast['title']}');";
      }
      $string .= "\n});";
      $string .= '</script>';
    }

    Session::forget('toasters');

    return new HtmlString($string);
  }

}
