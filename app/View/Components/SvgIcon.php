<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\File;

class SvgIcon extends Component
{
  public string $name;
  public string $class;

  public function __construct(string $name, string $class = '')
  {
    $this->name = $name;
    $this->class = $class;
  }

  public function render()
  {
    $path = public_path("icons/{$this->name}.svg");

    if (!File::exists($path)) {
      return '<!-- SVG not found -->';
    }

    $svg = file_get_contents($path);

    // Remove width and height attributes from the SVG tag
    $svg = preg_replace('/(width|height)="[\d\.]+"/', '', $svg);

    // Ensure viewBox exists for proper scaling
    if (!preg_match('/viewBox="[^"]+"/', $svg)) {
      $svg = preg_replace('/<svg(.*?)>/', '<svg$1 viewBox="0 0 24 24">', $svg, 1);
    }

    // Inject class into <svg> tag
    $svg = preg_replace('/<svg(.*?)>/', '<svg$1 class="' . $this->class . '">', $svg, 1);

    return $svg;
  }
}
