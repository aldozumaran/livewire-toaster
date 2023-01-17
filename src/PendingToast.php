<?php declare(strict_types=1);

namespace MAS\Toast;

use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @method PendingToast center()
 * @method PendingToast duration(int $milliseconds)
 * @method PendingToast error()
 * @method PendingToast info()
 * @method PendingToast left()
 * @method PendingToast message(string $message, array $replace = [])
 * @method PendingToast position(string $position)
 * @method PendingToast right()
 * @method PendingToast success()
 * @method PendingToast type(string $type)
 * @method PendingToast warning()
 */
final class PendingToast
{
    use ForwardsCalls;

    private ToastBuilder $builder;

    private bool $dispatched = false;

    private function __construct(int $duration, string $position)
    {
        $this->builder = ToastBuilder::create()
            ->duration($duration)
            ->position($position);
    }

    public static function make(int $duration, string $position): self
    {
        return new self($duration, $position);
    }

    public function dispatch(): void
    {
        $toast = $this->builder->get();

        Toaster::add($toast);

        $this->dispatched = true;
    }

    public function __call(string $name, array $arguments): mixed
    {
        $result = $this->forwardCallTo($this->builder, $name, $arguments);

        if ($result instanceof ToastBuilder) {
            $this->builder = $result;

            return $this;
        }

        return $result;
    }

    public function __destruct()
    {
        if (! $this->dispatched) {
            $this->dispatch();
        }
    }
}
