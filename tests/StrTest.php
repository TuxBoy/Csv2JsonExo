<?php
declare(strict_types=1);

use App\Test\Unit;
use App\Tools\Str;

abstract class Foo {

}

final class StrTest extends Unit
{

	public function testIsA(): void
	{
		$bar = new class extends Foo {};
		$foo = new class implements Countable {
			public function count()
			{
			}
		};
		$withoutExtends = new class {};

		$this->same(true, Str::isA($bar, Foo::class));
		$this->same(true, Str::isA($foo, Countable::class));
		$this->same(false, Str::isA($withoutExtends, Foo::class));
	}

}
