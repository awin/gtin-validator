<?php

declare(strict_types=1);

namespace Real\Validator\Tests\Gtin\Specification;

use PHPUnit\Framework\TestCase;
use Real\Validator\Gtin;

class PrefixTest extends TestCase
{
    public function testSpecificationInterfaceIsInherited(): void
    {
        $specification = new Gtin\Specification\Prefix();

        self::assertInstanceOf(Gtin\Specification::class, $specification);
    }

    public function testReasonCode(): void
    {
        $specification = new Gtin\Specification\Prefix();

        self::assertSame(1005, $specification->reasonCode());
    }

    public function prefixCounterProvider(): iterable
    {
        yield 'GTIN-8' => [8, 113 + 6];
        yield 'GTIN-12' => [12, 113 + 12];
        yield 'GTIN-13' => [13, 113 + 12];
        yield 'GTIN-14' => [14, 113 + 12];
    }

    /**
     * @dataProvider prefixCounterProvider
     */
    public function testListRanges(int $length, int $count): void
    {
        $specification = new Gtin\Specification\Prefix();

        /** @var Gtin $gtin */
        $gtin = $this->createConfiguredMock(Gtin::class, ['length' => $length]);

        $prefixes = $specification->listRanges($gtin);

        self::assertSame($count, count($prefixes));
    }

    public function prefixProvider(): iterable
    {
        yield '101' => ['101', 8, true];
        yield '099' => ['099', 8, true];
        yield '035' => ['035', 12, true];
        yield '025' => ['025', 12, true];
        yield '118' => ['118', 13, true];
        yield '140' => ['140', 13, false];
        yield '950' => ['950', 14, true];
        yield '956' => ['956', 14, false];
    }

    /**
     * @dataProvider prefixProvider
     */
    public function testIsSatisfied(string $prefix, int $length, bool $isSatisfied): void
    {
        $specification = new Gtin\Specification\Prefix();

        /** @var Gtin $gtin */
        $gtin = $this->createConfiguredMock(Gtin::class, [
            'length' => $length,
            'prefix' => $prefix,
        ]);

        self::assertSame($isSatisfied, $specification->isSatisfied($gtin));
    }

    public function additionalValidPrefixesProvider(): iterable
    {
        $ranges = [
            ['140', '199'],
            ['381', '381'],
            ['382', '382'],
            ['384', '384'],
            ['386', '386'],
            ['388', '388'],
            ['390', '399'],
            ['441', '449'],
            ['472', '472'],
            ['473', '473'],
            ['510', '519'],
            ['522', '527'],
            ['532', '534'],
            ['536', '538'],
            ['550', '559'],
            ['561', '568'],
            ['580', '589'],
            ['591', '593'],
            ['595', '598'],
            ['602', '602'],
            ['605', '605'],
            ['606', '606'],
            ['610', '610'],
            ['612', '612'],
            ['614', '614'],
            ['623', '623'],
            ['632', '639'],
            ['650', '679'],
            ['682', '689'],
            ['710', '728'],
            ['747', '749'],
            ['751', '753'],
            ['756', '758'],
            ['772', '772'],
            ['774', '774'],
            ['776', '776'],
            ['781', '783'],
            ['785', '785'],
            ['787', '787'],
            ['788', '788'],
        ];

        foreach ($ranges as [$lower, $upper]) {
            yield $lower => [$lower, 13, true]; // Valid prefix
            yield strval((int) $lower - 1) => [strval((int) $lower - 1), 13, false]; // Invalid below
            yield strval((int) $upper + 1) => [strval((int) $upper + 1), 13, false]; // Invalid above
        }
    }

    /**
     * @dataProvider additionalValidPrefixesProvider
     */
    public function testAdditionalValidPrefixes(string $prefix, bool $isSatisfied): void
    {
        $specification = new Gtin\Specification\Prefix();

        /** @var Gtin $gtin */
        $gtin = $this->createConfiguredMock(Gtin::class, [
            'prefix' => $prefix,
        ]);

        self::assertSame($isSatisfied, $specification->isSatisfied($gtin));
    }
}
