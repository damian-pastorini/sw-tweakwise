<?php declare(strict_types=1);

namespace RH\Tweakwise\Tests\Integration\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RH\Tweakwise\Service\FeedService;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

class GenerateFeedCommandTest extends TestCase
{
    use KernelTestBehaviour;

    public function testConsoleCommandExists(): void
    {
        $container = $this->getContainer();

        /** @var FeedService|MockObject $feedServiceMock */
        $feedServiceMock = $this->createMock(FeedService::class);
        $feedServiceMock->expects(self::once())->method('fixFeedRecords')->with(true);
        $feedServiceMock->expects(self::once())->method('generateScheduledFeeds');
        $feedServiceMock->expects(self::once())->method('scheduleFeeds');

        $container->set(FeedService::class, $feedServiceMock);

        $application = new Application($this->getKernel());
        $application->setAutoExit(false);

        self::assertTrue($application->has('tweakwise:generate-feed'));

        $tester = new ApplicationTester($application);
        $exitCode = $tester->run(['command' => 'tweakwise:generate-feed']);
        $output = $tester->getDisplay();
        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Creation of feeds took', $output);
    }
}
