<?php

namespace App\Controllers;

use App\Services\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DepartmentService;
use App\Validators\DepartmentValidator;

class DepartmentController
{
    private $departmentService;
    private $departmentValidator;
    private $logger;

    public function __construct(
        DepartmentService $departmentService,
        DepartmentValidator $departmentValidator,
        LoggerService $logger
    ) {
        $this->departmentService = $departmentService;
        $this->departmentValidator = $departmentValidator;
        $this->logger = $logger;
    }

    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->departmentValidator->validateCreate($data);

        if (!empty($errors)) {
            $this->logger->error("Validation errors on create department: " . json_encode($errors));

            return new Response(json_encode(['errors' => $errors]), 400, ['Content-Type' => 'application/json']);
        }

        $name = $data['name'];
        $parentId = $data['parent_id'] ?? null;
        $flags = $data['flags'] ?? 1;

        try {
            $this->departmentService->createDepartment($name, $parentId, $flags);

            return new Response("Department successfully created.", 201);
        } catch (\Exception $e) {
            $this->logger->error("Failed to create department: " . $e->getMessage());

            return new Response(
                "An error occurred while creating the department. Please try again later.",
                500
            );
        }
    }

    public function update(Request $request, int $id)
    {
        $idErrors = $this->departmentValidator->validateId($id);
        if (!empty($idErrors)) {
            $this->logger->error("Validation error on update department ID: " . json_encode($idErrors));

            return new Response(json_encode(['errors' => $idErrors]), 400, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getContent(), true);
        $errors = $this->departmentValidator->validateUpdate($data);

        if (!empty($errors)) {
            $this->logger->error("Validation errors on update department: " . json_encode($errors));

            return new Response(json_encode(['errors' => $errors]), 400, ['Content-Type' => 'application/json']);
        }

        $name = $data['name'];
        $parentId = $data['parent_id'] ?? null;
        $flags = $data['flags'] ?? 1;

        try {
            $this->departmentService->updateDepartment($id, $name, $parentId, $flags);
            return new Response("Department successfully updated.", 200);
        } catch (\Exception $e) {
            $this->logger->error("Failed to update department ID $id: " . $e->getMessage());

            return new Response(
                "An error occurred while updating the department. Please try again later.",
                500
            );
        }
    }

    public function delete(Request $request, int $id)
    {
        $idErrors = $this->departmentValidator->validateId($id);

        if (!empty($idErrors)) {
            $this->logger->error("Validation error on delete department ID: " . json_encode($idErrors));

            return new Response(
                json_encode(['errors' => $idErrors]),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        try {
            $this->departmentService->deleteDepartment($id);

            return new Response("Department successfully deleted.", 200);
        } catch (\Exception $e) {
            $this->logger->error("Failed to delete department ID $id: " . $e->getMessage());

            return new Response(
                "An error occurred while deleting the department. Please try again later.",
                500
            );
        }
    }

    public function get(Request $request, int $id)
    {
        $idErrors = $this->departmentValidator->validateId($id);
        if (!empty($idErrors)) {
            $this->logger->error("Validation error on get department ID: " . json_encode($idErrors));

            return new Response(json_encode(['errors' => $idErrors]), 400, ['Content-Type' => 'application/json']);
        }

        try {
            $department = $this->departmentService->getDepartment($id);

            if ($department) {
                return new Response(json_encode($department), 200, ['Content-Type' => 'application/json']);
            }
            return new Response("Department not found.", 404);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get department ID $id: " . $e->getMessage());

            return new Response(
                "An error occurred while retrieving the department. Please try again later.",
                500
            );
        }
    }

    public function getDescendantsByName(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? '';

        if (empty($name)) {
            return new Response("Name is required.", 400);
        }

        try {
            $descendants = $this->departmentService->getDirectDescendantsByName($name);

            return new Response(json_encode($descendants), 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get descendants by name $name: " . $e->getMessage());

            return new Response(
                "An error occurred while fetching the department descendants. Please try again later.",
                500
            );
        }
    }
}
