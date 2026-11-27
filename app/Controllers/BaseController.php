<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['form', 'url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /** Optional uploaded pictures are checked on create/update as well as text fields. */
    protected function optionalImageRule(string $field): array
    {
        $file = $this->request->getFile($field);
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return [];
        }
        return [$field => "uploaded[{$field}]|is_image[{$field}]|mime_in[{$field},image/jpeg,image/png,image/webp]|max_size[{$field},2048]"];
    }

    protected function rejectValidation()
    {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
}
