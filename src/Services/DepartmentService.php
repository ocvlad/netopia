<?php
namespace App\Services;

class DepartmentService
{
    private $db;
    private $logger;

    public function __construct(DatabaseService $databaseService, LoggerService $logger)
    {
        $this->db = $databaseService->getConnection();
        $this->logger = $logger;
    }

    public function createDepartment($name, $parentId, $flags)
    {
        $stmt = $this->db->prepare("CALL CreateDepartment(:name, :parentId, :flags)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':parentId', $parentId);
        $stmt->bindParam(':flags', $flags);

        if ($stmt->execute()) {
            $this->logger->info("Department '$name' created successfully.");
            return true;
        } else {
            $this->logger->error("Failed to create department '$name'.");
            return false;
        }
    }

    public function getDepartment($id)
    {
        $stmt = $this->db->prepare("CALL GetDepartmentById(:id)");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateDepartment($id, $name, $parentId, $flags)
    {
        $stmt = $this->db->prepare("CALL UpdateDepartment(:id, :name, :parentId, :flags)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':parentId', $parentId);
        $stmt->bindParam(':flags', $flags);

        if ($stmt->execute()) {
            $this->logger->info("Department '$id' updated successfully.");
            return true;
        } else {
            $this->logger->error("Failed to update department '$id'.");
            return false;
        }
    }

    public function deleteDepartment($id)
    {
        $stmt = $this->db->prepare("CALL DeleteDepartment(:id)");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $this->logger->info("Department '$id' deleted successfully.");
            return true;
        } else {
            $this->logger->error("Failed to delete department '$id'.");
            return false;
        }
    }

    public function getDirectDescendantsByName(string $name): array
    {
        $stmt = $this->db->prepare("CALL GetDirectDescendantsByName(:name)");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
