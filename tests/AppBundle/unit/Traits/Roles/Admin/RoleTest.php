<?php
declare(strict_types = 1);
/**
 * /tests/AppBundle/unit/Traits/Roles/Admin/RoleTest.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace AppBundle\unit\Traits\Roles\Admin;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleTest
 *
 * @package AppBundle\unit\Traits\Roles\Admin
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class RoleTest extends KernelTestCase
{
    /**
     * @dataProvider dataProviderTestThatTraitCallsExpectedMethod
     *
     * @param   string  $className
     * @param   string  $method
     * @param   string  $traitMethod
     * @param   array   $parameters
     */
    public function testThatTraitCallsExpectedMethod(
        string $className,
        string $method,
        string $traitMethod,
        array $parameters
    ) {
        $stub = $this->getMockForTrait(
            $className,
            [],
            '',
            true,
            true,
            true,
            [$traitMethod]
        );

        $stub
            ->expects(static::once())
            ->method($traitMethod)
            ->with(...$parameters);

        $result = call_user_func_array([$stub, $method], $parameters);

        static::assertInstanceOf(Response::class, $result);
    }

    /**
     * @return array
     */
    public function dataProviderTestThatTraitCallsExpectedMethod(): array
    {
        $pattern = __DIR__ . '/../../../../../../src/App/Traits/Rest/Roles/Admin/*.php';

        $iterator = function (string $filename): array {
            $name = str_replace('.php', '', basename($filename));

            $parameters = [
                $request = $this->createMock(Request::class),
            ];

            switch ($name) {
                case 'Delete':
                case 'FindOne':
                case 'Update':
                    $parameters[] = Uuid::uuid4()->toString();
                    break;
            }

            return [
                'App\\Traits\\Rest\\Roles\\Admin\\' . $name,
                lcfirst($name),
                lcfirst($name) . 'Method',
                $parameters,
            ];
        };

        return array_map($iterator, glob($pattern));
    }
}
