<?php declare(strict_types=1);

namespace Sturdy\Activity\Meta\Type;

/**
 * Integer type
 */
final class IntegerType
{
	private $minimumRange;
	private $maximumRange;
	private $step;

	/**
	 * Constructor
	 *
	 * @param string|null $state  the objects state
	 */
	public function __construct(string $state = null)
	{
		if ($state !== null) {
			[$min, $max, $step] = explode(",", $state);
			$this->minimumRange = strlen($min) ? (int)$min : null;
			$this->maximumRange = strlen($max) ? (int)$max : null;
			$this->step = strlen($step) ? (int)$step : null;
		}
	}

	/**
	 * Set meta properties on object
	 *
	 * @param stdClass $meta
	 */
	public function meta(stdClass $meta): void
	{
		$meta->type = "integer";
		if (isset($this->minimumRange)) {
			$meta->min = $this->minimumRange;
		}
		if (isset($this->maximumRange)) {
			$meta->max = $this->maximumRange;
		}
		if (isset($this->step)) {
			$meta->step = $this->step;
		}
	}

	/**
	 * Get state
	 *
	 * @return string
	 */
	public function getState(): string
	{
		return $this->minimumRange.",".$this->maximumRange.",".$this->step;
	}

	/**
	 * Set minimum range
	 *
	 * @param ?int $minimumRange
	 * @return self
	 */
	public function setMinimumRange(?int $minimumRange): self
	{
		$this->minimumRange = $minimumRange;
		return $this;
	}

	/**
	 * Get minimum range
	 *
	 * @return ?int
	 */
	public function getMinimumRange(): ?int
	{
		return $this->minimumRange;
	}

	/**
	 * Set maximum range
	 *
	 * @param ?int $maximumRange
	 * @return self
	 */
	public function setMaximumRange(?int $maximumRange): self
	{
		$this->maximumRange = $maximumRange;
		return $this;
	}

	/**
	 * Get maximum range
	 *
	 * @return ?int
	 */
	public function getMaximumRange(): ?int
	{
		return $this->maximumRange;
	}

	/**
	 * Set step
	 *
	 * @param ?int $step
	 * @return self
	 */
	public function setStep(?int $step): self
	{
		$this->step = $step;
		return $this;
	}

	/**
	 * Get step
	 *
	 * @return ?int
	 */
	public function getStep(): ?int
	{
		return $this->step;
	}

	/**
	 * Filter value
	 *
	 * @param  &$value  the value to filter
	 * @return bool  whether the value is valid
	 */
	public function filter(&$value): bool
	{
		$value = filter_var($value, FILTER_VALIDATE_INT);
		if ($value === false) {
			return false;
		}
		if (isset($this->minimumRange) && $value < $this->minimumRange) {
			return false;
		}
		if (isset($this->maximumRange) && $value > $this->maximumRange) {
			return false;
		}
		if (isset($this->step) && 0 !== (($value-($this->minimumRange??0)) % $this->step)) {
			return false;
		}
		return true;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return "integer";
	}
}
