<?php

namespace App\Livewire\Recruitment;

use App\Exceptions\AppException;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class BaseQuestionsIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    // Search
    public $search = '';

    // BaseQuestion section
    public $newQuestionModal = false;
    public $editQuestionModal = false;
    public $questionId;
    public $question;
    public $type;
    public $options;

    // Question types
    public $questionTypes = [];

    // Confirmation modal
    public $deleteConfirmationModal = false;
    public $itemToDelete;

    //reset pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Question functions
    public function openNewQuestionSec()
    {
        $this->newQuestionModal = true;
        $this->resetQuestionFields();
    }

    public function closeNewQuestionSec()
    {
        $this->newQuestionModal = false;
    }

    public function addNewQuestion()
    {
        $this->validate([
            'question' => 'required|string|min:3',
            'type' => 'required|in:' . implode(',', BaseQuestion::TYPES),
            'options' => 'nullable|string'
        ]);

        try {
            $baseQuestion = new BaseQuestion();
            $baseQuestion->createNewQuestion($this->question, $this->type, $this->options);
            
            $this->closeNewQuestionSec();
            $this->alertSuccess('Question added successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to add question. Please try again.');
        }
    }

    public function openEditQuestionSec($id)
    {
        $baseQuestion = BaseQuestion::find($id);
        $this->questionId = $baseQuestion->id;
        $this->question = $baseQuestion->question;
        $this->type = $baseQuestion->type;
        $this->options = $baseQuestion->options ? implode(',', $baseQuestion->options) : '';
        
        $this->editQuestionModal = true;
    }

    public function closeEditQuestionSec()
    {
        $this->editQuestionModal = false;
    }

    public function updateQuestion()
    {
        $this->validate([
            'question' => 'required|string|min:3',
            'type' => 'required|in:' . implode(',', BaseQuestion::TYPES),
            'options' => 'nullable|string'
        ]);

        try {
            $baseQuestion = BaseQuestion::find($this->questionId);
            $baseQuestion->updateQuestion($this->question, $this->type, $this->options);
            
            $this->closeEditQuestionSec();
            $this->alertSuccess('Question updated successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update question. Please try again.');
        }
    }

    public function confirmDeleteQuestion($id)
    {
        $this->itemToDelete = $id;
        $this->deleteConfirmationModal = true;
    }

    public function deleteQuestion()
    {
        try {
            $baseQuestion = BaseQuestion::find($this->itemToDelete);
            $baseQuestion->deleteQuestion();
            $this->closeDeleteConfirmationModal();
            $this->alertSuccess('Question deleted successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to delete question. Please try again.');
        }
    }

    // Helper functions
    public function closeDeleteConfirmationModal()
    {
        $this->deleteConfirmationModal = false;
        $this->itemToDelete = null;
    }

    private function resetQuestionFields()
    {
        $this->questionId = null;
        $this->question = null;
        $this->type = BaseQuestion::TYPE_TEXT;
        $this->options = null;
    }

    public function mount()
    {
        $this->questionTypes = BaseQuestion::TYPES;
    }

    public function render()
    {
        $baseQuestions = BaseQuestion::when($this->search, function ($query) {
                $query->where('question', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.recruitment.base-questions-index', [
            'baseQuestions' => $baseQuestions,
        ])->layout('components.layouts.app', ['title' => 'Base Questions', 'baseQuestionsIndex' => 'active']);
    }
} 