<?php
namespace Tests\Services;

use App\Services\DepartmentService;
use App\Services\DatabaseService;
use App\Services\LoggerService;
use PHPUnit\Framework\TestCase;

class DepartmentServiceTest extends TestCase
{
    private $mockDb;
    private $mockDbService;
    private $mockLogger;
    private $service;

    protected function setUp(): void
    {
        $this->mockDbService = $this->createMock(DatabaseService::class);
        $this->mockDb = $this->createMock(\PDO::class);
        $this->mockLogger = $this->createMock(LoggerService::class);

        $this->mockDbService->method('getConnection')->willReturn($this->mockDb);

        $this->service = new DepartmentService($this->mockDbService, $this->mockLogger);
    }

    public function testCreateDepartment()
    {
        $mockStmt = $this->createMock(\PDOStatement::class);
        $mockStmt->expects($this->once())->method('execute')->willReturn(true);
        $this->mockDb->method('prepare')->willReturn($mockStmt);

        $result = $this->service->createDepartment('Test Department', null, 1);
        $this->assertTrue($result, "Expected createDepartment to return true on success.");
    }

    public function testGetDepartment()
    {
        $mockStmt = $this->createMock(\PDOStatement::class);
        $mockStmt->expects($this->once())->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(['id' => 1, 'name' => 'Test Department']);
        $this->mockDb->method('prepare')->willReturn($mockStmt);

        $result = $this->service->getDepartment(1);
        $this->assertIsArray($result, "Expected getDepartment to return an array.");
        $this->assertEquals('Test Department', $result['name']);
    }

    public function testUpdateDepartment()
    {
        $mockStmt = $this->createMock(\PDOStatement::class);
        $mockStmt->expects($this->once())->method('execute')->willReturn(true);
        $this->mockDb->method('prepare')->willReturn($mockStmt);

        $result = $this->service->updateDepartment(1, 'Updated Department', null, 1);
        $this->assertTrue($result, "Expected updateDepartment to return true on success.");
    }

    public function testDeleteDepartment()
    {
        $mockStmt = $this->createMock(\PDOStatement::class);
        $mockStmt->expects($this->once())->method('execute')->willReturn(true);
        $this->mockDb->method('prepare')->willReturn($mockStmt);

        $result = $this->service->deleteDepartment(1);
        $this->assertTrue($result, "Expected deleteDepartment to return true on success.");
    }
}
