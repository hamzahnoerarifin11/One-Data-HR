<?php
namespace App\View\Components;

use Illuminate\View\Component;

class ChartCard extends Component
{
    /**
     * The card title.
     *
     * @var string|null
     */
    public ?string $title;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $title
     * @return void
     */
    public function __construct(?string $title = null)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.chart-card');
    }
}
