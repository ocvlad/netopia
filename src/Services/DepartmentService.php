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

    public function getAllDescendantsByName(string $name): array
    {
        $stmt = $this->db->prepare("CALL GetDepartmentHierarchyByName(:name)");
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        $departments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->buildHierarchy($departments);
    }

    private function buildHierarchy(array $departments): array
    {
        $map = [];
        $root = [];

        foreach ($departments as $department) {
            $department['children'] = [];
            $map[$department['id']] = $department;
        }

        foreach ($departments as $department) {
            if ($department['parent_id'] === null) {
                $root[] = &$map[$department['id']];
            } else {
                $map[$department['parent_id']]['children'][] = &$map[$department['id']];
            }
        }

        return $root;
    }
}
