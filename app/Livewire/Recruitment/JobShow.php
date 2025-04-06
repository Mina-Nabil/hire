<?php

namespace App\Livewire;

use App\Models\Applicant;
use App\Models\ApplicantNote;
use App\Models\Job;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use App\Models\City;
use App\Models\Location;

class JobShow extends Component
{
    use AlertFrontEnd;

    public $page_title = '• Jobs';
    public $job;
    public $question_text;
    public $question_text_ar;
    public $is_required = false;
    public $field_type = 'text';

    public $section = 'info';
    protected $queryString = ['section'];
    public function changeSection($section)
    {
        $this->section = $section;
        $this->mount($this->job->id);
    }

    public function clearProperty(string $propertyName)
    {
        // Check if the property exists before attempting to clear it
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = null;
        }
    }

    // START: FILTERS AGE
    public $isOpenFilterAge = false;
    public $ageOperator = '>=';
    public $ageValue = 0;

    public $EageOperator = '>=';
    public $EageValue = 0;

    public function openFilterAge(){
        $this->isOpenFilterAge = true;
    }

    public function closeFilterAge(){
        $this->reset(['isOpenFilterAge','EageOperator','EageValue']);
    }

    public function filterAge(){

        $this->validate([
            'EageOperator' => 'required|string|in:>=,<=,=',
            'EageValue' => 'required|integer|min:18'
        ]);

        $this->ageOperator = $this->EageOperator;
        $this->ageValue = $this->EageValue;

        $this->closeFilterAge();
    }

    public function clearFilterAge(){
        $this->ageOperator = '>=';
        $this->ageValue = 0;
    }

    public $applicantData = null;
    public $applicantAnswers = null;
    public $note_field;
    public $note_value;
    public $applicantNotes = [];

    public function openApplicantData($id){
        $this->applicantData = Applicant::findOrFail($id);
        $this->applicantAnswers = $this->applicantData->answers;
        $this->applicantNotes = $this->applicantData->notes;
    }

    public function closeApplicantData(){
        $this->applicantData = null;
        $this->applicantAnswers = null;
    }

    public function addNote()
    {
        $this->validate([
            'note_field' => 'required|string|max:255',
            'note_value' => 'required|string',
        ]);

        $note = $this->applicantData->addNote($this->note_field, $this->note_value);

        if ($note) {
            $this->applicantNotes[] = $note;
            $this->reset(['note_field', 'note_value']);
            $this->alertSuccess('Note added successfully!');
        } else {
            $this->alertFailed('Failed to add note.');
        }
    }

    public function deleteNote($noteId)
    {
        $note = ApplicantNote::findOrFail($noteId)->deleteNote();

        if ($note) {
            $this->mount($this->job->id);
            $this->applicantNotes = $this->applicantData->notes;
            $this->alertSuccess('Note deleted successfully!');
        } else {
            $this->alertFailed('Failed to delete note.');
        }
    }
    // END: FILTERS AGE

    // START: FILTERS ANSWER
    public $isOpenFilterAnswer = false;
    public $answerValue = '';
    public $EanswerValue = '';

    public function openFilterAnswer(){
        $this->isOpenFilterAnswer = true;
    }

    public function closeFilterAnswer(){
        $this->reset(['isOpenFilterAnswer', 'EanswerValue']);
    }

    public function filterAnswer(){
        $this->validate([
            'EanswerValue' => 'required|string|max:255'
        ]);

        $this->answerValue = $this->EanswerValue;

        $this->closeFilterAnswer();
    }

    public function clearFilterAnswer(){
        $this->answerValue = '';
    }
    // END: FILTERS ANSWER

    // START: FILTERS STATUS
    public $isOpenFilterStatus = false;
    public $statusValue = '';
    public $EstatusValue = '';

    public function openFilterStatus(){
        $this->isOpenFilterStatus = true;
    }

    public function closeFilterStatus(){
        $this->reset(['isOpenFilterStatus', 'EstatusValue']);
    }

    public function filterStatus(){
        $this->validate([
            'EstatusValue' => 'required|string|max:255'
        ]);

        $this->statusValue = $this->EstatusValue;

        $this->closeFilterStatus();
        $this->applicantData = $this->applicantData;
    }

    public function clearFilterStatus(){
        $this->statusValue = '';
    }
    // END: FILTERS STATUS

    // START: FILTERS LOCATION
    public $isOpenFilterLocation = false;
    public $cityId;
    public $city_id;
    public $city;
    public $locationId;
    public $location_id;
    public $locations = [];
    public $location;
    public $filterByCity = false;

    public function openFilterLocation()
    {
        $this->isOpenFilterLocation = true;
    }

    public function closeFilterLocation()
    {
        $this->reset(['isOpenFilterLocation', 'cityId', 'locationId', 'locations']);
    }

    public function updatedCityId()
    {
        $this->locations = Location::where('city_id', $this->cityId)->get();
    }

    public function filterLocation()
    {
        $this->validate([
            'cityId' => 'required|exists:cities,id',
        ]);

        if ($this->filterByCity) {
            $this->location = null;
            $this->location_id = null;
            $this->reset(['locationId', 'locations','location_id']);
            $this->city_id = $this->cityId;
            $this->city = City::findOrFail($this->cityId);

        } else {
            $this->validate([
                'locationId' => 'required|exists:locations,id',
            ]);
            $this->reset(['city_id','city','cityId']);
            $this->location = Location::findOrFail($this->locationId);
            $this->location_id = $this->locationId;
        }

        $this->closeFilterLocation();
    }

    public function clearFilterLocation()
    {
        $this->reset(['cityId','city_id','city','location','location_id', 'locationId', 'locations']);
    }
    // END: FILTERS LOCATION

    public function downloadCv($id)
    {
        $response = Applicant::findOrFail($id)->downloadCv();
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $this->alertFailed(' CV file not found');
        } else {
            return $response;
        }
    }

    public $deleteQuestionId;

    public function openConfirmDeleteQuestion($id){
        $this->deleteQuestionId = $id;
    }

    public function closeConfirmDeleteQuestion(){
        $this->deleteQuestionId = null;
    }

    public function deleteQuestion(){
        $res = Question::findORFail($this->deleteQuestionId)->deleteQuestion();
        if ($res) {
            $this->closeConfirmDeleteQuestion();
            $this->alertSuccess('Question deleted!');
            $this->mount($this->job->id);
        }else{
            $this->alertFailed();
        }
    }

    // START: DELETE APPLICANT
    public $deleteApplicantId;

    public function openConfirmDeleteApplicant($id)
    {
        $this->deleteApplicantId = $id;
    }

    public function closeConfirmDeleteApplicant()
    {
        $this->deleteApplicantId = null;
    }

    public function deleteApplicant()
    {
        $applicant = Applicant::findOrFail($this->deleteApplicantId);
        $res = $applicant->deleteApplicant();
        
        if ($res) {
            $this->closeConfirmDeleteApplicant();
            $this->alertSuccess('Applicant deleted successfully!');
            $this->mount($this->job->id);
        } else {
            $this->alertFailed('Failed to delete applicant.');
        }
    }
    // END: DELETE APPLICANT

    public $isOpenEditQuestion = null;

    public function openEditQuesSection($id)
    {
        $q = Question::findORFail($id);
        $this->question_text = $q->question_text;
        $this->question_text_ar = $q->question_text_ar;
        $this->field_type = $q->field_type;
        $this->is_required = $q->is_required ? true : false;
        $this->isOpenEditQuestion = $q;
    }

    public function closeEditQuesSection(){
        $this->reset(['isOpenEditQuestion','question_text','question_text_ar','is_required','field_type']);
    }

    public function editQuestion(){
        $this->validate([
            'question_text' => 'required|string|max:255',
            'question_text_ar' => 'nullable|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number',
            'is_required' => 'boolean'
        ]);

        $question = $this->isOpenEditQuestion;
        $res = $question->editQuestion(
            $this->question_text,
            $this->is_required,
            $this->field_type,
            $this->question_text_ar
        );

        if ($res) {
            $this->closeEditQuesSection();
            $this->mount($this->job->id);
            $this->alertSuccess('Question updated!');
        }else{
            $this->alertFailed();
        }
    }

    public $isOpenNewQuestion = false;

    public function openNewQuesSection()
    {
        $this->isOpenNewQuestion = true;
    }

    public function closeNewQuesSection()
    {
        $this->reset(['isOpenNewQuestion','is_required', 'question_text', 'question_text_ar', 'field_type']);
    }

    public function addQuestion(){
        $this->validate([
            'question_text' => 'required|string|max:255',
            'question_text_ar' => 'nullable|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number',
            'is_required' => 'boolean'
        ]);

        $res = $this->job->addQuestion(
            $this->question_text,
            $this->field_type,
            $this->is_required,
            $this->question_text_ar
        );

        if ($res) {
            $this->closeNewQuesSection();
            $this->mount($this->job->id);
            $this->alertSuccess('Question added!');
        }else{
            $this->alertFailed();
        }
    }

    public function setStatus($status){
        $this->job->setStatus($status);
        $this->alertSuccess('Status updated');
    }

    public function setApplicantStatus($id, $status)
    {
        $applicant = Applicant::findOrFail($id);
        $res = $applicant->setStatus($status);

        if ($res) {
            $this->alertSuccess('Applicant status updated!');
            $this->mount($this->job->id);
        } else {
            $this->alertFailed();
        }
    }

    public $cities;

    public function mount($id)
    {
        $this->job = Job::findOrFail($id);
        $this->cities = City::all();
    }

    // START: FILTERS CHANNEL
    public $isOpenFilterChannel = false;
    public $channelValue = '';
    public $EchannelValue = '';

    public function openFilterChannel()
    {
        $this->isOpenFilterChannel = true;
    }

    public function closeFilterChannel()
    {
        $this->reset(['isOpenFilterChannel', 'EchannelValue']);
    }

    public function filterChannel()
    {
        $this->validate([
            'EchannelValue' => 'required|string|in:LinkedIn,CFA Society,AMCham,Recruitment Agency,Others'
        ]);

        $this->channelValue = $this->EchannelValue;
        $this->closeFilterChannel();
    }

    public function clearFilterChannel()
    {
        $this->channelValue = '';
    }
    // END: FILTERS CHANNEL

    public function render()
    {
        $applicants = $this->job->filterApplicants(
            $this->ageOperator, 
            $this->ageValue, 
            $this->answerValue, 
            $this->statusValue, 
            $this->location_id, 
            $this->city_id,
            $this->channelValue
        );

        $statuses = Job::STATUSES;
        $applicant_statuses = Applicant::STATUSES;
        return view('livewire.job-show', [
            'statuses' => $statuses,
            'applicants' => $applicants,
            'applicant_statuses' => $applicant_statuses,
            'cities' => $this->cities,
        ])->layout('layouts.admin', ['page_title' => $this->page_title, 'jobs' => 'active']);
    }
}
