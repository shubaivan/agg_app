<?php


namespace App\Controller\Rest\Admin;

use App\Entity\AttachmentFilesInterface;
use App\Entity\Files;
use App\Repository\CategoryRepository;
use App\Repository\FilesRepository;
use Entity\Category;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Rest\AbstractRestController;
use App\Services\Helpers;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class AttachmentFileController extends AbstractRestController
{
    /**
     * @var FilesRepository
     */
    private $fileRepo;

    /**
     * @var CategoryRepository
     */
    private $categoryRepo;

    /**
     * AttachmentFileController constructor.
     * @param FilesRepository $fileRepo
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Helpers $helpers,
        FilesRepository $fileRepo,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($helpers);
        $this->fileRepo = $fileRepo;
        $this->categoryRepo = $categoryRepository;
    }

    /**
     * create attachment file.
     *
     * @Rest\Post("/admin/api/attachment_file", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={Files::GROUP_GET})
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return Files[]
     * @throws \Exception
     */
    public function postAttachmentFileAction(Request $request)
    {
        /** @var UploadedFile[] $files */
        $files = $request->files->get('files');

        switch ($request->get('entity')) {
            case Category::class:
                $repo = $this->categoryRepo;
                break;
            default:
                $repo = false;
        }
        if (!$repo) {
            throw new \Exception('entity was not matched');
        }
        /** @var AttachmentFilesInterface $parentEntity */
        $parentEntity = $repo->findOneBy(['id' => $request->get('id')]);
        if (!$parentEntity) {
            throw new \Exception('entity not found');
        }
        $response = [];
        foreach ($files as $file) {
            if ('blob' === $file->getClientOriginalName()) {
                continue;
            }
            $check = $parentEntity->checkFileExist($file->getClientOriginalName());
            if ($check) {
                throw new \Exception('exist file');
            }
            $entityFile = new Files();
            $entityFile
                ->setBufferEntity($parentEntity)
                ->setPath($file);

            if ($request->get('caption')) {
                $entityFile
                    ->setDescription($request->get('caption'));
            }

            $this->fileRepo->save($entityFile);
            $response[] = $entityFile;
        }

        return $response;
    }

    /**
     * list attachment files.
     *
     * @Rest\Post("/admin/api/attachment_files/list", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={Files::GROUP_GET})
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return array
     * @throws \Exception
     */
    public function getAttachmentFilesListAction(Request $request)
    {
        switch ($request->get('entity')) {
            case Category::class:
                $repo = $this->categoryRepo;
                break;
            default:
                $repo = false;
        }
        if (!$repo) {
            throw new \Exception('entity was not matched');
        }
        /** @var AttachmentFilesInterface $parentEntity */
        $parentEntity = $repo->findOneBy(['id' => $request->get('id')]);
        if (!$parentEntity) {
            throw new \Exception('entity not found');
        }

        return $parentEntity->getFiles()->getValues();
    }

    /**
     * remove attachment file.
     *
     * @Rest\Delete("/admin/api/attachment_file/{id}", options={"expose": true})
     *
     * @param Request $request
     *
     * @View(statusCode=Response::HTTP_OK, serializerGroups={Files::GROUP_GET})
     *
     * @SWG\Tag(name="Admin")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Json collection object",
     * )
     *
     * @return array
     * @throws \Exception
     */
    public function deleteAttachmentFileAction(Files $file)
    {
        $this->fileRepo->remove($file);

        return ['status' => 'success'];
    }
}