<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Experience;
use Validator;
use Auth;

use Techno\Transformers\ExperienceTransformer;

class ExperienceController extends ApiController
{

    /**
     * @var Acme\Transformers\LessonTransformer
     */
    protected $experienceTransformer;

    function __construct(ExperienceTransformer $experienceTransformer)
    {
        $this->middleware('auth:api', ['only' => [
            'index', 'show', 'store', 
            'update', 'delete',
            'create'             

        ]]);
        $this->experienceTransformer = $experienceTransformer;
    }

	public function index()
    {

        $experiences =  Experience::where('user_id','=', auth()->id())->get();


        return $this->respondWithSuccess("Experience List",
            $this->experienceTransformer->transformCollection($experiences)
        );
    }

    public function show(Experience $experience)
    {

        if (Auth::user()->cant('update', $experience)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the experience");
        }

        if(! $experience)
        {            
            return $this->respondNotFound('Experience does not exist');
        }


        return $this->respondWithSuccess("Experience Detail", 
            $this->experienceTransformer->transform($experience->toArray())
        );
    }

    public function store(Request $request)
    {
        if (Auth::user()->cant('create', Experience::class)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the experience");
        }

        $input = $request->all();

        $validator = $this->validateResource($input);

        if ($validator != null)
        {
            
            return $this->respondWithValidationError($validator);
        }

        $experience = $this->create($input);

        // return response()->json($experience, 201);
        return $this->respondCreated($message = "Experience created",
            $this->experienceTransformer->transform($experience->toArray())
        );
    }

    protected function validateResource($input)
    {
        // $messages = [
        //     'older_than' => 'Age must be greater than 18.',
        // ];
        $rules = [
            'designation' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'from_month' => 'required',
            'from_year' => 'required',
            'is_currently_working' => 'required',           
            
        ];

        // $validator = Validator::make($input, $rules, $messages);
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return $validator->errors();
        }
        return null;
    }

    /**
     * Create a new user experience instance after a valid experience.
     *
     * @param  array  $data
     * @return Experience
     */
    protected function create(array $data)
    {
        $input_data = [
            'user_id'               => Auth::user()->id,
            'designation'           => isset($data['designation']) ? $data['designation'] : null,
            'company_name'          => isset($data['company_name']) ? $data['company_name'] : null,
            'location'              => isset($data['location']) ? $data['location'] : null,
            'latitude'              => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude'             => isset($data['longitude']) ? $data['longitude'] : null,
            'from_month'            => isset($data['from_month']) ? $data['from_month'] : null,
            'from_year'             => isset($data['from_year']) ? $data['from_year'] : null,
            'is_currently_working'  => isset($data['is_currently_working']) ? $data['is_currently_working'] : null,
            'to_month'              => isset($data['to_month']) ? $data['to_month'] : null,
            'to_year'               => isset($data['to_year']) ? $data['to_year'] : null
        ];
        //dd($input_data);
        return Experience::create($input_data);
    }


    public function update(Request $request, Experience $experience)
    {
        if (Auth::user()->cant('update', $experience)) {
            return $this->respondWithForbiddenError($message = "You are not the owner of the experience");
        }
        //dd(request()->all());
        $input = $request->all();

        $validator = $this->validateResource($input);

        if ($validator != null)
        {            
            return $this->respondWithValidationError($validator);
        }
        

        $experience->update($input);
        //dd($experience);
        // return response()->json($experience, 201);
        return $this->respondWithSuccess($message = "Experience updated",
            $this->experienceTransformer->transform($experience->toArray())
        );


    }

    public function delete(Experience $experience)
    {
        if (Auth::user()->cant('delete', $experience)) {
            return $this->respondWithForbiddenError();
        }
        $experience->delete();
        return $this->respondDeleted();
        
    }
}