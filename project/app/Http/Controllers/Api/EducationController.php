<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Education;
use Validator;
use Auth;

use Techno\Transformers\EducationTransformer;

class EducationController extends ApiController
{

    /**
     * @var Acme\Transformers\LessonTransformer
     */
    protected $educationTransformer;

    function __construct(EducationTransformer $educationTransformer)
    {
        $this->middleware('auth:api', ['only' => [
            'index', 'show', 'store', 
            'update', 'delete',
            'create'             

        ]]);
        $this->educationTransformer = $educationTransformer;
    }

	public function index()
    {

        $educations =  Education::where('user_id','=', auth()->id())->get();


        return $this->respondWithSuccess("Education List",
            $this->educationTransformer->transformCollection($educations)
        );
    }

    public function show(Education $education)
    {

        if (Auth::user()->cant('update', $education)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the education");
        }

        if(! $education)
        {            
            return $this->respondNotFound('Education does not exist');
        }


        return $this->respondWithSuccess("Education Detail", 
            $this->educationTransformer->transform($education->toArray())
        );
    }

    public function store(Request $request)
    {
        if (Auth::user()->cant('create', Education::class)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the education");
        }

        $input = $request->all();

        $validator = $this->validateResource($input);

        if ($validator != null)
        {
            
            return $this->respondWithValidationError($validator);
        }

        $education = $this->create($input);

        // return response()->json($education, 201);
        return $this->respondCreated($message = "Education created",
            $this->educationTransformer->transform($education->toArray())
        );
    }

    protected function validateResource($input)
    {
        // $messages = [
        //     'older_than' => 'Age must be greater than 18.',
        // ];
        $rules = [
            'school_college_name' => 'required|string|max:255',
            'board_university_name' => 'required|string|max:255',
            'degree_name' => 'required|string|max:255'          
            
        ];

        // $validator = Validator::make($input, $rules, $messages);
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return $validator->errors();
        }
        return null;
    }

    /**
     * Create a new user education instance after a valid education.
     *
     * @param  array  $data
     * @return Education
     */
    protected function create(array $data)
    {
        $input_data = [
            'user_id'                   => Auth::user()->id,
            'school_college_name'       => isset($data['school_college_name']) ? $data['school_college_name'] : null,
            'board_university_name'     => isset($data['board_university_name']) ? $data['board_university_name'] : null,
            'degree_name'               => isset($data['degree_name']) ? $data['degree_name'] : null
        ];
        //dd($input_data);
        return Education::create($input_data);
    }


    public function update(Request $request, Education $education)
    {
        if (Auth::user()->cant('update', $education)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the education");
        }
        //dd(request()->all());
        $input = $request->all();

        $validator = $this->validateResource($input);

        if ($validator != null)
        {            
            return $this->respondWithValidationError($validator);
        }
        

        $education->update($input);
        //dd($education);
        // return response()->json($education, 201);
        return $this->respondWithSuccess($message = "Education updated",
            $this->educationTransformer->transform($education->toArray())
        );


    }

    public function delete(Education $education)
    {
        if (Auth::user()->cant('delete', $education)) {
            return $this->respondWithForbiddenError();
        }
        $education->delete();
        return $this->respondDeleted();
        
    }
}