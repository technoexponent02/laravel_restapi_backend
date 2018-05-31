<?php
namespace App\Http\Controllers\Api;

use Response;
use App\Http\Controllers\Controller;

class ApiController extends Controller {
    protected $statusCode = 200;
    /**
     * @return mixed
     */
    public function __construct()
    {
        // $response = ['Unauthorized action.'];
        // abort(403, 'Unauthorized action.');

    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondWithValidationError($message = 'Error Validation')
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }


    public function respondWithInternalError( $message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }
    /**
     * The user is authenticated, but does not have the permissions to perform an action.
     *
     * @param  string $message [description]
     * @return [type]          [description]
     */
    public function respondWithForbiddenError( $message = 'Forbidden Error')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    public function respondCreated( $message = 'Resource created', $data, $headers = [])
    {
        return $this->setStatusCode(201)->respondWithSuccess($message, $data, $headers);
    }

    public function respondDeleted()
    {
        //dd($this->setStatusCode(204)->respondWithSuccess($message, $data, $headers));
        return $this->setStatusCode(204)->respond($data = [], $headers = []);
    }

    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message' => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }
    public function respondWithSuccess($message = "Resource", $data, $headers = [])
    {
        return $this->respond([
            'response' => [
                'message' => $message,
                'status_code' => $this->getStatusCode(),
                'data' => $data
            ]
        ], $headers);
    }

}